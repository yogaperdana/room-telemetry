import sys
import time
import glob
import pickle
import serial
import pymysql
import datetime
import threading
from tkinter import *
from tkinter import ttk, messagebox
from time import gmtime, strftime

CONFIG_FILE = '.config'
try:
    with open(CONFIG_FILE, 'rb') as f:
        config_var = pickle.load(f)
    time_trans = config_var[0]
    time_dbase = config_var[1]
    sql_hostname = config_var[2]
    sql_username = config_var[3]
    sql_password = config_var[4]
except:
    time_trans = 5
    time_dbase = 5
    sql_hostname = 'localhost'
    sql_username = sql_password = ''

sensordata = [0,0,0,0,0,0,0,0]
timeout = 5
ser_open = 0
sql_conn = 0
thread_created = 0
thrser_created = 0
time_trans_btn = 0
time_trans_cur = time_trans
time1 = ''

def serial_ports():
    if sys.platform.startswith('win'):
        ports = ['COM%s' % (i + 1) for i in range(256)]
    elif sys.platform.startswith('linux') or sys.platform.startswith('cygwin'):
        ports = glob.glob('/dev/tty[A-Za-z]*')
    else:
        raise EnvironmentError('Platform tidak didukung')
    result = ['']
    for port in ports:
        try:
            s = serial.Serial(port)
            s.close()
            result.append(port)
        except (OSError, serial.SerialException):
            pass
    return result

class LDRTeleViewer(Frame):
    def __init__(self, master=None):
        Frame.__init__(self, master)
        self.pack(fill=BOTH, expand=False)
        self.createWidgets()
        
    def mysql_connect(self):
        global sql_conn
        global sql_hostname
        global sql_username
        global sql_password
        global thread_created
        sql_hostname = self.sqlhe.get()
        sql_username = self.sqlue.get()
        sql_password = self.sqlpe.get()
        if sql_hostname and sql_username and sql_password:
            try:
                db = pymysql.connect(self.sqlhe.get(), self.sqlue.get(), self.sqlpe.get())
                cursor = db.cursor()
                self.sct.insert(END, "Koneksi ke server MySQL berhasil.\n")
                self.sct.see(END)
                self.sqlhe.configure(state=DISABLED)
                self.sqlue.configure(state=DISABLED)
                self.sqlpe.configure(state=DISABLED)
                self.sqlcb.config(text="Berhenti", command=self.mysql_reset)
                sql_conn = 1
                for t in threading.enumerate():
                    n = t.getName()
                    if not n.startswith(('MainThread', 'SockThread', 'SerialThread')):
                        if n:
                            thread_created = 1
                        else:
                            thread_created = 0
                if thread_created == 0:
                    t = threading.Thread(target=self.mysql_thread_insert, name='MySQLThread')
                    t.daemon = True
                    t.start()
            except pymysql.MySQLError as e:
                self.mysql_stop(e.args[0], e.args[1])
            
    def mysql_stop(self, er1, er2):
        self.sct.insert(END, strftime("%d-%m-%Y %H:%M:%S"))
        self.sct.insert(END, " - Koneksi gagal atau terputus!\n" +
            "Error {}: {}\n".format(er1, er2))
        self.sct.see(END)
        self.mysql_reset()
        
    def mysql_reset(self):
        global sql_conn
        sql_conn = 0
        self.sqlhe.configure(state=NORMAL)
        self.sqlue.configure(state=NORMAL)
        self.sqlpe.configure(state=NORMAL)
        self.sqlcb.config(text="Sambung", command=self.mysql_connect)
        self.sct.insert(END, "Mohon sambungkan ke server MySQL.\n")
        self.sct.see(END)
        
    def mysql_thread_insert(self):
        self.sct.insert(END, "Menambahkan data...\n")
        self.sct.see(END)
        db = pymysql.connect(self.sqlhe.get(), self.sqlue.get(), self.sqlpe.get())
        cursor = db.cursor()
        infail = 0
        dv = {}
        mit = time.time()
        time_dbase_cur = 0.0
        while True:
            try:
                if sql_conn == 1:
                    mft = time.time()
                    if float(round(mft, 0)) == float(round(mit, 0)+time_dbase_cur):
                        mit = time.time()
                        time_dbase_cur = float(time_dbase)
                        dd = strftime("%d-%m-%Y %H:%M:%S ")
                        for s in range(0,8):
                            if sensordata[s] == '':
                                dv[s] = 0
                            else:
                                dv[s] = int(sensordata[s])
                            dd += "%4s " % (str(dv[s]),)
                        sql = "INSERT INTO `telemetri`.`sensor_aata`(S1, S2, S3, S4, S5, S6, S7, S8) \
                               VALUES ('%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d')" % \
                               (dv[0], dv[1], dv[2], dv[3], dv[4], dv[5], dv[6], dv[7])
                        try:
                            cursor.execute(sql)
                            db.commit()
                            infail = 0
                            self.sct.insert(END, "" + dd + "\n")
                            self.sct.see(END)
                        except pymysql.MySQLError as e:
                            db.rollback()
                            if infail == 0:
                                self.sct.insert(END, strftime("%d-%m-%Y %H:%M:%S"))
                                self.sct.insert(END, " - Terdapat kesalahan\n" +
                                    "Error {}: {}\n".format(e.args[0], e.args[1]))
                                self.sct.see(END)
                            infail = 1
                else:
                    mit = time.time()
                    time_dbase_cur = 0.0
                self.update()
            except:
                pass

    def style_reset(self):
        self.listport['state'] = "readonly"
        self.selport.config(text="Pilih", command=self.get_cb_val)
        self.itt.configure(state="readonly")
        self.itb.configure(state=DISABLED)
        for s in range(1,9):
            self.re[s].delete(0, END)
        self.txt.insert(END, 'Mohon pilih port telemetri.')
        self.txt.see(END)
        
    def get_time_trans_val(self):
        global time_trans
        global time_trans_btn
        if self.itt.get():
            if int(self.itt.get()) == 0:
                time_trans = 1
                self.itt.delete(0, END)
                self.itt.insert(END, '1')
            else:
                time_trans = self.itt.get()
            time_trans_btn = 1

    def get_time_dbase_val(self):
        global time_dbase
        if self.idt.get():
            if self.idt.get() == 0:
                time_dbase = 1
                self.idt.delete(0, END)
                self.idt.insert(END, '1')
            else:
                time_dbase = self.idt.get()
            
    def btn_stop(self):
        global ser_open
        ser_open = 0
        self.txt.insert(END, '\n')
        
    def get_cb_val(self):
        global thrser_created
        if self.port_val.get():
            ser_open = 1
            for t in threading.enumerate():
                n = t.getName()
                if not n.startswith(('MainThread', 'SockThread', 'MySQLThread')):
                    if n:
                        thrser_created = 1
                    else:
                        thrser_created = 0
            if thrser_created == 0:
                t = threading.Thread(target=self.readingserial, name='SerialThread')
                t.daemon = True
                t.start()
        else:
            messagebox.showwarning("Perhatian", "Mohon pilih port telemetri!")
            
    def readingserial(self):
        global timeout
        global ser_open
        global time_trans_btn
        global time_trans_cur
        global sensordata
        ser = serial.Serial(port=self.port_val.get(), baudrate=115200, timeout=0)
        if ser.isOpen():
            ser_open = 1
            self.listport['state'] = "disabled"
            self.selport.config(text="Stop", command=self.btn_stop)
            self.itt.configure(state=NORMAL)
            self.itb.configure(state=NORMAL)
            self.txt.insert(END, "\nMenggunakan port " + self.port_val.get())
            self.txt.see(END)
        it = time.time()
        sdt = 0
        z = ""
        xh = '['
        xf = ']'
        while True:
            try:
                if ser_open == 0:
                    ser.close()
                    self.style_reset()
                    sensordata = [0,0,0,0,0,0,0,0]
                    break
                if ser_open == 1:
                    if time_trans_btn == 1:
                        q = ser.write(b"TIME=" + time_trans.encode())
                        time_trans_cur = time_trans
                        time_trans_btn = 0
                    x = str(ser.readline(), "utf-8")
                    if x:
                        it = time.time()
                        if ('[' in x) and (']' in x):
                            z = x
                        else:
                            z += x
                        if ('[' in z) and (']' in z):
                            zi = z[z.find(xh)+1 : z.find(xf)]
                            zs = zi.split('|')
                            self.txt.insert(END, strftime("\n%d-%m-%Y %H:%M:%S"))
                            for q, ze in enumerate(zs):
                                if zs[q] == '':
                                    zs[q] = 0
                                if self.re[q+1].get() == '':
                                    sensordata[q] = 0
                                else:
                                    sensordata[q] = zs[q]
                                self.re[q+1].delete(0, END)
                                self.re[q+1].insert(END, zs[q])
                                self.txt.insert(END, " %4s" % (str(zs[q]),))
                            z = ""
                            self.txt.see(END)
                    else:
                        ft = time.time()
                        dt = round(ft-it, 0)
                        if dt == (int(time_trans)*int(timeout)):
                            self.txt.insert(END, strftime("\n%d-%m-%Y %H:%M:%S"))
                            self.txt.insert(END, ' - Tidak Ada Data Baru\n' +
                                "Transmisi data kemungkinan berhenti atau tidak tersedia.\n" +
                                "Mohon periksa kembali perangkat telemetri.")
                            self.txt.see(END)
                            it = time.time()
                self.update()
            except:
                ser.close()
                break
                pass

    def tick(self):
        global time1
        time2 = time.strftime(' %d-%m-%Y %H:%M:%S ')
        if time2 != time1:
            time1 = time2
            self.sbar.config(text=time2)
        self.sbar.after(500, self.tick)
    
    def createWidgets(self):
        self.fr0c0 = LabelFrame(self, text=" Port Telemetri ")
        self.fr0c0.grid(row=0, column=0, sticky=NW, padx=(15,10), pady=(10,0))
        self.port_val = StringVar()
        self.listport = ttk.Combobox(
            self.fr0c0,
            textvariable=self.port_val,
            width=8,
            state='readonly'
        )
        self.listport['values'] = serial_ports()
        self.listport.current(0)
        self.listport.grid(row=0, column=0, padx=8, pady=5)
        self.selport = Button(
            self.fr0c0,
            text="Pilih",
            command=self.get_cb_val,
            width=5,
            pady=0,
            relief=GROOVE,
            foreground="black"
        )
        self.selport.grid(row=0, column=1, padx=(0,8), pady=5)
        self.re = {}
        self.rf = LabelFrame(self, text=" Nilai ADC Sensor ")
        for p in range(0,16):
            r = (p//2)+1
            if (p%2) == 0:
                self.snt = Label(self.rf, text="{0}".format(r))
                self.snt.grid(row=0, column=p, sticky=N, padx=(5,3), pady=(6,4))
            if (p%2) == 1:
                self.re[r] = Entry(self.rf, width=5, justify=CENTER)
                self.re[r].grid(row=0, column=p, padx=(0,5), pady=(7,8))
        self.rf.grid(row=0, column=1, sticky=N+W+E, padx=(0,15), pady=(10,0))
        self.fr2c0 = LabelFrame(self, text=" Interval Transmisi ")
        self.fr2c0.grid(row=2, column=0, sticky=NW, padx=(15,10), pady=(10,0))
        self.itt = Entry(self.fr2c0, width=6, justify=CENTER)
        self.itt.grid(row=0, column=0, padx=(8,0), pady=(5,8))
        self.itt.delete(0, END)
        self.itt.insert(END, time_trans)
        self.itm = Label(self.fr2c0, text="detik")
        self.itm.grid(row=0, column=1, sticky=N, padx=(2,0), pady=5)
        self.itb = Button(
            self.fr2c0,
            text="Ubah",
            command=self.get_time_trans_val,
            width=5,
            pady=0,
            relief=GROOVE
        )
        self.itb.grid(row=0, column=2, padx=(5,8), pady=5)
        self.fr3c0 = LabelFrame(self, text=" Interval Database ")
        self.fr3c0.grid(row=3, column=0, sticky=NW, padx=(15,10), pady=(10,0))
        self.idt = Entry(self.fr3c0, width=6, justify=CENTER)
        self.idt.grid(row=0, column=0, padx=(8,0), pady=(5,8))
        self.idt.delete(0, END)
        self.idt.insert(END, time_dbase)
        self.idm = Label(self.fr3c0, text="detik")
        self.idm.grid(row=0, column=1, sticky=N, padx=(2,0), pady=5)
        self.idb = Button(
            self.fr3c0,
            text="Ubah",
            command=self.get_time_dbase_val,
            width=5,
            pady=0,
            relief=GROOVE
        )
        self.idb.grid(row=0, column=2, padx=(5,8), pady=5)
        self.gta = Frame(self)
        self.gta.grid(row=2, column=1, rowspan=3, columnspan=1, sticky=NW, padx=(0,10), pady=(10,0))
        self.fta1 = LabelFrame(self.gta, text=" Riwayat Data ")
        self.fta1.grid(row=0, column=0, sticky=N+W+E, padx=(0,5), pady=(0,10))
        self.con = Frame(self.fta1, borderwidth=2, relief=SUNKEN)
        self.con.grid_rowconfigure(0, weight=1)
        self.con.grid_columnconfigure(0, weight=1)
        self.ysb = Scrollbar(self.con)
        self.ysb.grid(row=0, column=1, sticky=NS)
        self.txt = Text(
            self.con,
            wrap=WORD,
            borderwidth=0,
            yscrollcommand=self.ysb.set,
            height=8,
            width=63,
            font=('Courier', '9')
        )
        self.txt.grid(row=0, column=0, sticky=N+W+E, padx=0, pady=0)
        self.ysb.config(command=self.txt.yview)
        self.con.grid(padx=8, pady=(5,8))
        self.fr4c0 = LabelFrame(self, text=" Koneksi MySQL ")
        self.fr4c0.grid(row=4, column=0, sticky=NW, padx=(15,10), pady=(10,0))
        self.sqlhl = Label(self.fr4c0, text="Hostname:")
        self.sqlhl.grid(row=0, column=0, padx=(4,0), pady=(0,0), sticky=NW)
        self.sqlul = Label(self.fr4c0, text="Username:")
        self.sqlul.grid(row=2, column=0, padx=(4,0), pady=(0,0), sticky=NW)
        self.sqlpl = Label(self.fr4c0, text="Password:")
        self.sqlpl.grid(row=4, column=0, padx=(4,0), pady=(0,0), sticky=NW)
        self.sqlhe = Entry(self.fr4c0, width=20)
        self.sqlhe.grid(row=1, column=0, padx=8, pady=(0,8), sticky=NW)
        self.sqlhe.delete(0, END)
        self.sqlhe.insert(END, sql_hostname)
        self.sqlue = Entry(self.fr4c0, width=20)
        self.sqlue.grid(row=3, column=0, padx=8, pady=(0,8), sticky=NW)
        self.sqlue.delete(0, END)
        self.sqlue.insert(END, sql_username)
        self.sqlpe = Entry(self.fr4c0, width=20, show="*")
        self.sqlpe.grid(row=5, column=0, padx=8, pady=(0,8), sticky=NW)
        self.sqlpe.delete(0, END)
        self.sqlpe.insert(END, sql_password)
        self.sqlcb = Button(
            self.fr4c0,
            text="Sambung",
            command=self.mysql_connect,
            width=10,
            pady=0,
            relief=GROOVE
        )
        self.sqlcb.grid(row=6, column=0, padx=8, pady=(2,8), sticky=NW)
        self.fta2 = LabelFrame(self.gta, text=" Pantauan Database ")
        self.fta2.grid(row=1, column=0, sticky=N+W+E, padx=(0,5), pady=(0,0))
        self.scf = Frame(self.fta2, borderwidth=2, relief=SUNKEN)
        self.scf.grid_rowconfigure(0, weight=1)
        self.scf.grid_columnconfigure(0, weight=1)
        self.scy = Scrollbar(self.scf)
        self.scy.grid(row=0, column=1, sticky=NS)
        self.sct = Text(
            self.scf,
            wrap=WORD,
            borderwidth=0,
            yscrollcommand=self.scy.set,
            height=8,
            width=63,
            font=('Courier', '9')
        )
        self.sct.grid(row=0, column=0, sticky=N+W+E, padx=0, pady=0)
        self.scy.config(command=self.sct.yview)
        self.scf.grid(padx=8, pady=5)
        self.sbar = Label(self, bd=1, relief=SUNKEN, anchor=E)
        self.sbar.grid(row=5, column=0, columnspan=3, sticky=W+E+S, padx=0, pady=(15,0))
        self.tick()
        self.style_reset()
        self.mysql_reset()
        return self.sqlhe.get()

def main():
    root = Tk()
    root.title("Telemetri Sensor")
    root.geometry("668x430")
    root.resizable(width=FALSE, height=FALSE)
    app = LDRTeleViewer(root)
    def exit_fn():
        sql_conn == 0
        config_var = [
            time_trans, time_dbase, sql_hostname, sql_username, sql_password
        ]
        with open(CONFIG_FILE, 'wb') as f:
            pickle.dump(config_var, f, pickle.HIGHEST_PROTOCOL)
        root.destroy()
        sys.exit()
    root.protocol("WM_DELETE_WINDOW", exit_fn)
    root.mainloop()

if __name__ == '__main__':
    main()
