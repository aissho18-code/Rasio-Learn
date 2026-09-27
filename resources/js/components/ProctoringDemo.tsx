import React, { useState, useEffect, useRef } from 'react';

export default function ProctoringDemo() {
  const [examStatus, setExamStatus] = useState<'idle' | 'running' | 'blocked' | 'finished'>('idle');
  const [violationCount, setViolationCount] = useState(0);
  const [logs, setLogs] = useState<any[]>([]);
  const [showWarning, setShowWarning] = useState(false);
  const [warningMessage, setWarningMessage] = useState('');
  const [maxViolationsGlobal, setMaxViolationsGlobal] = useState<number>(3);

  const student = { id: 'stu-100293', name: 'Budi Santoso', kelas: 'XII IPA 1' };
  const [examList, setExamList] = useState<any[]>([
    { id: 'exam-1', title: 'Ujian Biologi - Semester 1', locked: false, maxViolations: 3 },
    { id: 'exam-2', title: 'Quiz Matematika Modul 2', locked: false, maxViolations: 2 },
  ]);

  const [activeExamId, setActiveExamId] = useState<string | null>('exam-1');

  const examStatusRef = useRef(examStatus);
  useEffect(()=>{ examStatusRef.current = examStatus; }, [examStatus]);

  // Integration ke Backend Laravel
  const sendLogToServer = async (logData: any) => {
    try {
      const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;
      await fetch('/admin/proctoring/log', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
        },
        body: JSON.stringify(logData),
      });
    } catch (err) {
      console.error('Gagal mengirim log ke server Laravel:', err);
    }
  };

  const logViolation = (type: string, detail: string, severity: 'info'|'warn'|'critical' = 'warn', extra = {}) => {
    const timestamp = new Date().toLocaleTimeString();
    const newLog = {
      id: Date.now().toString(),
      time: timestamp,
      studentId: student.id,
      examId: activeExamId,
      type,
      detail,
      severity,
      ...extra,
    };

    setLogs(prev => [newLog, ...prev]);

    setViolationCount(prev => {
      const newCount = prev + (severity === 'critical' ? 2 : 1);
      const effectiveMax = getActiveExamSetting('maxViolations') ?? maxViolationsGlobal;
      if (newCount >= effectiveMax) {
        setExamStatus('blocked');
      }
      return newCount;
    });

    setWarningMessage(`Pelanggaran terdeteksi: ${type}`);
    setShowWarning(true);
    setTimeout(() => setShowWarning(false), 3000);

    sendLogToServer(newLog);
  };

  const getActiveExamSetting = (key: string) => {
    if (!activeExamId) return null;
    const ex = examList.find(e => e.id === activeExamId);
    return ex ? ex[key] : null;
  };

  useEffect(() => {
    if (examStatus !== 'running') return;

    const handleBlur = () => {
      logViolation('Pindah Tab/Aplikasi', 'Siswa keluar dari layar ujian (Window kehilangan fokus).', 'warn');
    };

    const handleVisibility = () => {
      if (document.visibilityState === 'hidden') {
        logViolation('Tab Tersembunyi', 'Tab menjadi tersembunyi atau user pindah aplikasi.', 'warn');
      }
    };

    const handleContextMenu = (e: Event) => {
      e.preventDefault();
      logViolation('Klik Kanan', 'Siswa mencoba membuka menu konteks (klik kanan).', 'info');
    };

    const handleKeyDown = (e: KeyboardEvent) => {
      const key = e.key.toLowerCase();
      if ((e.ctrlKey || e.metaKey) && (key === 'c' || key === 'v')) {
        e.preventDefault();
        logViolation('Copy-Paste', `Siswa menekan kombinasi (Ctrl/Cmd+${key.toUpperCase()}).`, 'warn');
      }
      if (e.key === 'F12') {
        e.preventDefault();
        logViolation('Developer Tools', 'Siswa mencoba membuka inspect element (F12).', 'critical');
      }
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && key === 'i') {
        e.preventDefault();
        logViolation('Developer Tools', 'Siswa mencoba membuka developer tools (Ctrl/Cmd+Shift+I).', 'critical');
      }
    };

    const handleCopy = (e: ClipboardEvent) => {
      e.preventDefault();
      logViolation('Copy Data', 'Siswa mencoba menyalin teks dari soal.', 'warn');
    };

    const handlePaste = (e: ClipboardEvent) => {
      e.preventDefault();
      logViolation('Paste Data', 'Siswa mencoba menempelkan teks selama ujian.', 'warn');
    };

    const handleMouseLeave = () => {
      logViolation('Pointer Keluar', 'Pointer/Mouse meninggalkan area browser.', 'info');
    };

    window.addEventListener('blur', handleBlur);
    document.addEventListener('visibilitychange', handleVisibility);
    window.addEventListener('contextmenu', handleContextMenu);
    window.addEventListener('keydown', handleKeyDown as any);
    window.addEventListener('copy', handleCopy as any);
    window.addEventListener('paste', handlePaste as any);
    document.addEventListener('mouseleave', handleMouseLeave);

    return () => {
      window.removeEventListener('blur', handleBlur);
      document.removeEventListener('visibilitychange', handleVisibility);
      window.removeEventListener('contextmenu', handleContextMenu);
      window.removeEventListener('keydown', handleKeyDown as any);
      window.removeEventListener('copy', handleCopy as any);
      window.removeEventListener('paste', handlePaste as any);
      document.removeEventListener('mouseleave', handleMouseLeave);
    };
  }, [examStatus, activeExamId, examList, maxViolationsGlobal]);

  const handleTeacherReset = () => {
    setViolationCount(0);
    setExamStatus('running');
    setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: 'Guru/Proktor mereset status ujian siswa.', studentId: student.id, severity: 'info' }, ...prev]);
  };

  const handleTeacherForceSubmit = () => {
    setExamStatus('finished');
    setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: 'Guru/Proktor menghentikan paksa ujian siswa.', studentId: student.id, severity: 'critical' }, ...prev]);
  };

  const toggleLockExam = (examId: string) => {
    setExamList(prev => prev.map(e => e.id === examId ? { ...e, locked: !e.locked } : e));
    setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: `Admin mengubah lock exam ${examId}.`, severity: 'info', examId }, ...prev]);
  };

  const deleteExam = (examId: string) => {
    if (!confirm('Hapus ujian ini?')) return;
    setExamList(prev => prev.filter(e => e.id !== examId));
    setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: `Admin menghapus exam ${examId}.`, severity: 'info', examId }, ...prev]);
    if (activeExamId === examId) setActiveExamId(null);
  };

  const editExam = (examId: string) => {
    const ex = examList.find(e => e.id === examId);
    const newTitle = prompt('Edit judul ujian:', ex?.title ?? '');
    if (newTitle != null) {
      setExamList(prev => prev.map(e => e.id === examId ? { ...e, title: newTitle } : e));
      setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: `Admin mengubah judul exam ${examId}.`, severity: 'info', examId }, ...prev]);
    }
  };

  const setExamMaxViolations = (examId: string) => {
    const valStr = prompt('Masukkan jumlah maksimal pelanggaran:', String(getActiveExamSetting('maxViolations') ?? maxViolationsGlobal));
    const val = Number(valStr);
    if (!Number.isNaN(val) && val > 0) {
      setExamList(prev => prev.map(e => e.id === examId ? { ...e, maxViolations: val } : e));
      setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: `Admin menetapkan maxViolations=${val} untuk ${examId}.`, severity: 'info', examId }, ...prev]);
    }
  };

  const exportLogsCSV = () => {
    if (logs.length === 0) { alert('Tidak ada log untuk diekspor.'); return; }
    const header = ['time','studentId','examId','type','detail','severity'];
    const rows = logs.map(l => header.map(h => `"${(l[h] ?? '').toString().replace(/"/g,'""')}"`).join(','));
    const csv = [header.join(','), ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `proctoring_logs_${Date.now()}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  };

  const renderStudentPOV = () => (
    <div className="flex-1 bg-white border-r border-slate-200 flex flex-col relative overflow-hidden">
      <div className="bg-blue-600 text-white p-4 shadow-md flex justify-between items-center z-10">
        <div>
          <h2 className="text-lg font-bold">POV Siswa (Layar Ujian)</h2>
          <p className="text-sm opacity-80">{student.name} - {student.kelas}</p>
        </div>
        <div className="bg-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
          Sisa Waktu: 59:20
        </div>
      </div>

      <div className="p-6 flex-1 overflow-y-auto relative bg-slate-50">
        <div className="absolute inset-0 pointer-events-none flex items-center justify-center opacity-5">
          <h1 className="text-6xl font-black transform -rotate-45">{student.name.toUpperCase()} - {student.id}</h1>
        </div>

        {examStatus === 'idle' && (
          <div className="h-full flex flex-col items-center justify-center text-center">
            <h3 className="text-xl font-bold mb-4 text-slate-800">Ujian Siap Dimulai</h3>
            <p className="text-slate-600 mb-6 max-w-md">Klik tombol di bawah untuk memulai. Setelah dimulai, jangan pindah tab atau melakukan copy-paste.</p>
            <div className="flex gap-3">
              <select value={activeExamId || ''} onChange={(e) => setActiveExamId(e.target.value)} className="p-2 border rounded">
                <option value="">Pilih Ujian</option>
                {examList.map(ex => <option key={ex.id} value={ex.id}>{ex.title}{ex.locked ? ' (Locked)' : ''}</option>)}
              </select>
              <button 
                onClick={() => {
                  if (!activeExamId) { alert('Pilih ujian terlebih dahulu.'); return; }
                  const ex = examList.find(e => e.id === activeExamId);
                  if (ex?.locked) { alert('Ujian ini terkunci oleh admin.'); return; }
                  setExamStatus('running');
                  setViolationCount(0);
                  setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: `Siswa memulai exam ${activeExamId}.`, studentId: student.id, examId: activeExamId, severity:'info' }, ...prev]);
                }}
                className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold transition-colors shadow-lg"
              >
                Mulai Ujian Sekarang
              </button>
            </div>
          </div>
        )}

        {examStatus === 'running' && (
           <div className="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm border border-slate-200">
             <div className="flex justify-between items-center mb-6">
                <span className="font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-md">Soal No. 1</span>
                <div className="text-xs text-slate-400">Exam: {examList.find(x=>x.id===activeExamId)?.title}</div>
             </div>
             <p className="text-lg text-slate-800 mb-6 select-none">
                Perhatikan paragraf berikut!<br/><br/>
                "Berdasarkan analisis struktur sel, organel yang berfungsi sebagai pusat respirasi seluler dan menghasilkan energi dalam bentuk ATP adalah..."
             </p>
             <div className="space-y-3">
               {['Nukleus', 'Mitokondria', 'Ribosom', 'Badan Golgi'].map((opt, i) => (
                 <label key={i} className="flex items-center p-4 border border-slate-200 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                   <input type="radio" name="q1" className="w-5 h-5 text-blue-600" />
                   <span className="ml-3 text-slate-700">{opt}</span>
                 </label>
               ))}
             </div>
             <div className="mt-8 pt-6 border-t border-slate-100 flex justify-between items-center">
                <div className="text-sm text-slate-500">Pelanggaran: <strong>{violationCount}</strong></div>
                <div>
                  <button 
                    onClick={() => {
                      setExamStatus('finished');
                      setLogs(prev => [{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Sistem', detail: `Siswa selesai & kumpulkan ${activeExamId}.`, studentId: student.id, examId: activeExamId, severity:'info' }, ...prev]);
                    }}
                    className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors"
                  >
                    Selesai & Kumpulkan
                  </button>
                </div>
             </div>
           </div>
        )}

        {(examStatus === 'blocked' || examStatus === 'finished') && (
          <div className="h-full flex flex-col items-center justify-center text-center">
            <div className={`w-20 h-20 rounded-full flex items-center justify-center mb-4 ${examStatus === 'blocked' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'}`}>
              <svg className="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d={examStatus === 'blocked' ? "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" : "M5 13l4 4L19 7"}></path></svg>
            </div>
            <h3 className={`text-xl font-bold mb-2 ${examStatus === 'blocked' ? 'text-red-700' : 'text-slate-800'}`}>
              {examStatus === 'blocked' ? 'Ujian Diblokir!' : 'Ujian Selesai'}
            </h3>
            <p className="text-slate-600 max-w-sm">
              {examStatus === 'blocked' 
                ? `Anda telah melanggar kebijakan ujian (total pelanggaran: ${violationCount}). Ujian dikunci. Hubungi pengawas.` 
                : 'Terima kasih, jawaban Anda telah tersimpan di sistem.'}
            </p>
          </div>
        )}
      </div>

      {showWarning && (
        <div className="absolute inset-0 bg-red-600/90 flex flex-col items-center justify-center text-white z-50 animate-pulse">
          <svg className="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          <h2 className="text-3xl font-black mb-2">PERINGATAN!</h2>
          <p className="text-xl">{warningMessage}</p>
        </div>
      )}
    </div>
  );

  const renderTeacherPOV = () => (
    <div className="flex-1 bg-slate-900 text-slate-300 flex flex-col h-full overflow-hidden">
      <div className="bg-slate-950 p-4 shadow-md flex justify-between items-center z-10 border-b border-slate-800">
        <div>
          <h2 className="text-lg font-bold text-white">POV Guru (Dashboard Pengawas)</h2>
          <p className="text-sm text-emerald-400 flex items-center mt-1">
            <span className="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
            Sistem Aktif & Terhubung
          </p>
        </div>
      </div>

      <div className="p-6 flex-1 overflow-y-auto">
        <div className="bg-slate-800 rounded-xl p-5 border border-slate-700 shadow-xl mb-6">
          <div className="flex justify-between items-start mb-4">
            <div>
              <h3 className="text-white font-bold text-lg">{student.name}</h3>
              <p className="text-sm text-slate-400">NIS: {student.id} | Kelas: {student.kelas}</p>
            </div>
            <div className={`px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider ${
              examStatus === 'idle' ? 'bg-slate-700 text-slate-300' :
              examStatus === 'running' ? 'bg-emerald-900/50 text-emerald-400 border border-emerald-800' :
              examStatus === 'finished' ? 'bg-blue-900/50 text-blue-400 border border-blue-800' :
              'bg-red-900/50 text-red-400 border border-red-800'
            }`}>
              {examStatus === 'running' ? 'Sedang Ujian' : examStatus}
            </div>
          </div>

          <div className="flex items-center justify-between py-4 border-y border-slate-700 mb-4">
            <div className="text-center">
              <p className="text-xs text-slate-400 uppercase">Tingkat Kecurangan</p>
              <p className={`text-3xl font-black mt-1 ${violationCount >= maxViolationsGlobal ? 'text-red-500' : violationCount > 0 ? 'text-yellow-500' : 'text-emerald-500'}`}>
                {violationCount} <span className="text-sm font-normal text-slate-500">/ {maxViolationsGlobal}</span>
              </p>
            </div>
            <div className="flex gap-2">
              <button 
                onClick={handleTeacherReset}
                disabled={violationCount === 0 && examStatus !== 'blocked'}
                className="bg-slate-700 hover:bg-slate-600 disabled:opacity-50 text-white px-4 py-2 rounded text-sm transition-colors"
              >
                Reset Status
              </button>
              <button 
                onClick={handleTeacherForceSubmit}
                disabled={examStatus !== 'running'}
                className="bg-red-900/80 hover:bg-red-800 disabled:opacity-50 text-white px-4 py-2 rounded text-sm transition-colors border border-red-700"
              >
                Force Submit
              </button>
            </div>
          </div>
        </div>

        <h3 className="text-white font-bold mb-3 flex items-center">
          <svg className="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Live Activity Logs
        </h3>

        <div className="space-y-3">
          {logs.length === 0 ? (
            <p className="text-sm text-slate-500 italic bg-slate-800/50 p-4 rounded-lg border border-slate-700/50 text-center">Menunggu aktivitas siswa...</p>
          ) : (
            logs.map((log) => (
              <div key={log.id} className={`bg-slate-800 p-3 rounded-lg border-l-4 ${log.severity === 'critical' ? 'border-red-500' : log.severity === 'warn' ? 'border-yellow-400' : 'border-blue-400'} text-sm shadow-sm`}>
                <div className="flex justify-between mb-1">
                  <span className={`font-bold ${log.severity === 'critical' ? 'text-red-400' : log.severity === 'warn' ? 'text-yellow-300' : 'text-blue-300'}`}>{log.type}</span>
                  <span className="text-slate-500 text-xs">{log.time}</span>
                </div>
                <p className="text-slate-300">{log.detail}</p>
                <div className="text-xs text-slate-500 mt-1">Exam: {log.examId ?? '-'}</div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  );

  const renderAdminPOV = () => (
    <div className="flex-1 bg-white/5 text-slate-900 flex flex-col h-full overflow-auto">
      <div className="bg-white p-4 shadow-sm flex justify-between items-center border-b border-slate-200">
        <div>
          <h2 className="text-lg font-bold">Admin — Manajemen Ujian / Quiz</h2>
          <p className="text-sm text-slate-500">Di sini admin bisa edit/hapus/lock ujian serta melihat ringkasan kecurangan.</p>
        </div>

        <div className="flex items-center gap-3">
          <button onClick={() => {
            const title = prompt('Judul ujian baru:');
            if (!title) return;
            const id = `exam-${Date.now()}`;
            setExamList(prev => [{ id, title, locked: false, maxViolations: maxViolationsGlobal }, ...prev]);
          }} className="px-3 py-2 bg-blue-600 text-white rounded">Buat Ujian Baru</button>

          <button onClick={() => {
            const v = prompt('Set global max violations:', String(maxViolationsGlobal));
            const n = Number(v);
            if (!Number.isNaN(n) && n > 0) setMaxViolationsGlobal(n);
          }} className="px-3 py-2 bg-slate-100 text-slate-800 rounded">Kebijakan (Max: {maxViolationsGlobal})</button>

          <button onClick={exportLogsCSV} className="px-3 py-2 bg-slate-100 text-slate-800 rounded">Export Logs</button>
        </div>
      </div>

      <div className="p-6 space-y-6">
        <div className="bg-white rounded shadow p-4">
          <h3 className="font-semibold mb-3">Daftar Ujian / Quiz</h3>
          <table className="w-full text-sm">
            <thead className="bg-slate-50">
              <tr>
                <th className="p-2 text-left">Judul</th>
                <th className="p-2 text-center">Status</th>
                <th className="p-2 text-center">Max Violations</th>
                <th className="p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {examList.map(ex => (
                <tr key={ex.id} className="border-t">
                  <td className="p-2">{ex.title}</td>
                  <td className="p-2 text-center">{ex.locked ? 'Locked' : 'Active'}</td>
                  <td className="p-2 text-center">{ex.maxViolations ?? maxViolationsGlobal}</td>
                  <td className="p-2">
                    <div className="flex gap-2">
                      <button onClick={() => editExam(ex.id)} className="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Edit</button>
                      <button onClick={() => toggleLockExam(ex.id)} className="px-2 py-1 bg-slate-100 text-slate-800 rounded">{ex.locked ? 'Unlock' : 'Lock'}</button>
                      <button onClick={() => setExamMaxViolations(ex.id)} className="px-2 py-1 bg-indigo-100 text-indigo-800 rounded">Set Policy</button>
                      <button onClick={() => deleteExam(ex.id)} className="px-2 py-1 bg-red-100 text-red-800 rounded">Hapus</button>
                    </div>
                  </td>
                </tr>
              ))}
              {examList.length === 0 && <tr><td colSpan={4} className="p-4 text-center text-slate-500">Belum ada ujian.</td></tr>}
            </tbody>
          </table>
        </div>

        <div className="bg-white rounded shadow p-4">
          <h3 className="font-semibold mb-3">Ringkasan Kecurangan (Demo)</h3>
          <div className="mb-4 text-sm text-slate-600">Ringkasan pelanggaran per siswa (dari log demo).</div>

          <div className="space-y-3">
            <div className="flex items-center justify-between p-3 border rounded">
              <div>
                <div className="font-semibold">Budi Santoso</div>
                <div className="text-xs text-slate-500">ID: {student.id} | Kelas: {student.kelas}</div>
              </div>
              <div className="text-right">
                <div className="text-xl font-bold">{logs.filter(l => l.studentId === student.id).length}</div>
                <div className="text-xs text-slate-500">Total pelanggaran</div>
                <div className="flex gap-2 mt-2">
                  <button onClick={() => { setExamStatus('blocked'); setLogs(prev=>[{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Admin Action', detail: 'Admin memblokir siswa', studentId: student.id, severity:'critical' }, ...prev]); }} className="px-3 py-1 bg-red-600 text-white rounded">Block</button>
                  <button onClick={() => { setViolationCount(0); setLogs(prev=>[{ id: Date.now().toString(), time: new Date().toLocaleTimeString(), type: 'Admin Action', detail: 'Admin reset pelanggaran siswa', studentId: student.id, severity:'info' }, ...prev]); }} className="px-3 py-1 bg-slate-100 text-slate-800 rounded">Reset</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white rounded shadow p-4">
          <h3 className="font-semibold mb-3">Detail Logs</h3>
          <div className="space-y-2">
            {logs.length === 0 ? <div className="text-slate-500 text-sm">Belum ada logs.</div> : logs.map(l => (
              <div key={l.id} className="p-3 border rounded flex justify-between items-start">
                <div>
                  <div className="font-semibold">{l.type} <span className="text-xs text-slate-400">({l.severity})</span></div>
                  <div className="text-xs text-slate-600">{l.detail}</div>
                  <div className="text-xs text-slate-400 mt-1">Student: {l.studentId} • Exam: {l.examId ?? '-'}</div>
                </div>
                <div className="text-xs text-slate-400">{l.time}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <div className="h-screen w-full font-sans bg-slate-50 flex flex-col">
      <div className="bg-slate-100 p-2 text-center border-b border-slate-200">
        <p className="text-slate-600 text-sm">Demo Proctoring — Student | Teacher | Admin panels (left → right).</p>
      </div>

      <div className="flex-1 flex overflow-hidden">
        {renderStudentPOV()}
        {renderTeacherPOV()}
        {renderAdminPOV()}
      </div>
    </div>
  );
}