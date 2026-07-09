import { spawnSync } from 'node:child_process';
import { existsSync, mkdirSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const root = resolve(__dirname, '..');
const publicDir = join(root, 'public');
const outputRoot = join(publicDir, 'mockup');
const tempRoot = join(root, 'storage', 'app', 'mockup-html');
const logoUrl = pathToFileURL(join(publicDir, 'logo.png')).href;

const chromeCandidates = [
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
    'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
];

const chromePath = chromeCandidates.find((candidate) => existsSync(candidate));

if (!chromePath) {
    console.error('Chrome atau Edge tidak ditemukan untuk render screenshot.');
    process.exit(1);
}

rmSync(tempRoot, { recursive: true, force: true });
mkdirSync(tempRoot, { recursive: true });

const roleLabels = {
    admin: 'ADMIN',
    siswa: 'SISWA',
    'orang-tua': 'ORANG TUA',
    pembina: 'PEMBINA',
};

const users = {
    admin: { name: 'Admin Utama', initials: 'AU' },
    siswa: { name: 'Rafi Pratama', initials: 'RP' },
    'orang-tua': { name: 'Ibu Sari', initials: 'IS' },
    pembina: { name: 'Budi Santoso', initials: 'BS' },
};

const menus = {
    admin: ['Dashboard', 'Kelola Akun', 'Data Siswa', 'Data Orang Tua', 'Data Pembina', 'Data Eskul', 'Laporan'],
    siswa: ['Dashboard', 'Daftar Eskul', 'Absensi', 'Laporan Saya'],
    'orang-tua': ['Dashboard', 'Validasi Pendaftaran', 'Laporan Anak'],
    pembina: ['Dashboard', 'Validasi Pendaftaran', 'Jadwal Eskul', 'Kelola Absensi', 'Input Penilaian', 'Buat Laporan'],
};

const statusMap = {
    aktif: ['Aktif', 'success'],
    nonaktif: ['Nonaktif', 'secondary'],
    menunggu_validasi_orang_tua: ['Menunggu Validasi Orang Tua', 'warning'],
    menunggu_validasi_pembina: ['Menunggu Validasi Pembina', 'info'],
    diterima: ['Diterima', 'success'],
    ditolak_orang_tua: ['Ditolak Orang Tua', 'danger'],
    ditolak_pembina: ['Ditolak Pembina', 'danger'],
    menunggu_approval: ['Menunggu Approval', 'warning'],
    disetujui: ['Disetujui', 'success'],
    ditolak: ['Ditolak', 'danger'],
    belum_daftar: ['Belum Daftar', 'light'],
};

const css = `
*{box-sizing:border-box}
body{margin:0;background:#f4f7fa;color:#37474f;font-family:"Open Sans",Arial,sans-serif;font-size:14px}
.auth-body{min-height:100vh;background:#f4f7fa;display:flex;align-items:center;justify-content:center}
.auth-card{width:430px;background:#fff;border-radius:4px;box-shadow:0 1px 20px rgba(69,90,100,.08);padding:42px 38px;text-align:center}
.auth-logo{width:96px;height:96px;object-fit:contain;margin:0 auto 24px}
.auth-title{font-size:21px;font-weight:400;margin:0 0 8px;color:#37474f}
.auth-subtitle{margin:0 0 28px;color:#748892}
.layout{min-height:100vh}
.sidebar{position:fixed;top:0;left:0;bottom:0;width:264px;background:#fff;color:#535763;box-shadow:0 0 11px rgba(0,0,0,.13);z-index:2}
.brand{height:70px;background:#101b33;color:#fff;display:flex;align-items:center;padding:0 22px;gap:12px;font-weight:700}
.brand img{width:38px;height:38px;object-fit:contain}
.caption{padding:24px 24px 10px;font-size:11px;font-weight:700;letter-spacing:.04em;color:#97a7c1}
.menu a{height:44px;display:flex;align-items:center;gap:12px;padding:0 22px;color:#535763;text-decoration:none}
.menu a.active{background:#4680ff;color:#fff}
.menu-icon{width:25px;height:25px;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;background:#eef3ff;color:#4680ff;font-size:12px;font-weight:700}
.menu a.active .menu-icon{background:rgba(255,255,255,.18);color:#fff}
.topbar{position:fixed;top:0;left:264px;right:0;height:70px;background:#4680ff;color:rgba(255,255,255,.9);display:flex;align-items:center;justify-content:space-between;padding:0 28px;z-index:1}
.profile{display:flex;align-items:center;gap:11px;font-weight:600}
.avatar{width:38px;height:38px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#fff;color:#1f4fb2;font-weight:700}
.content{margin-left:264px;padding:94px 28px 28px}
.page-head{margin-bottom:20px}
.page-head h1{font-size:20px;font-weight:600;margin:0 0 8px;color:#37474f}
.breadcrumb{color:#748892;font-size:13px}
.row{display:grid;grid-template-columns:repeat(12,1fr);gap:18px}
.col-12{grid-column:span 12}.col-10{grid-column:span 10}.col-9{grid-column:span 9}.col-8{grid-column:span 8}.col-6{grid-column:span 6}.col-5{grid-column:span 5}.col-4{grid-column:span 4}.col-3{grid-column:span 3}.col-2{grid-column:span 2}
.card{background:#fff;border-radius:4px;box-shadow:0 1px 20px rgba(69,90,100,.08);margin-bottom:18px}
.card.border{box-shadow:none;border:1px solid #e2e5e8}
.card-header{border-bottom:1px solid #e2e5e8;padding:17px 22px;display:flex;align-items:center;justify-content:space-between}
.card-header h5{font-size:15px;margin:0;font-weight:600;color:#37474f}
.card-body{padding:22px}
.stat h6{font-size:14px;margin:0 0 14px;color:#37474f;font-weight:600}
.stat h3{font-size:28px;margin:0;color:#37474f;font-weight:600}
.stat .meta{color:#748892;margin-top:6px}
.stat-icon{font-size:28px;color:#4680ff;text-align:right;font-weight:700}
.form-row{display:grid;grid-template-columns:repeat(12,1fr);gap:14px;margin-bottom:16px}
.field{margin-bottom:16px}
.field label{display:block;margin-bottom:7px;color:#37474f;font-weight:500}
.control{height:43px;border:1px solid #ced4da;border-radius:3px;background:#fff;color:#495057;padding:10px 12px;display:flex;align-items:center;justify-content:space-between}
.textarea{height:92px;align-items:flex-start;line-height:1.45}
.muted{color:#748892}
.filter{margin-bottom:16px}
.btn{border:0;border-radius:3px;padding:9px 13px;display:inline-flex;align-items:center;gap:7px;font-size:13px;color:#fff;text-decoration:none;white-space:nowrap}
.btn-sm{padding:7px 10px;font-size:12px}
.btn-primary{background:#4680ff}.btn-secondary{background:#6c757d}.btn-info{background:#3ebfea}.btn-success{background:#1de9b6;color:#123}.btn-warning{background:#f4c22b;color:#2b2b2b}.btn-danger{background:#f44236}.btn-light{background:#f4f7fa;color:#37474f;border:1px solid #e2e5e8}
.btn-block{justify-content:center;width:100%}
.actions{display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap}
.table-wrap{overflow:hidden;border-radius:3px}
table{width:100%;border-collapse:collapse;background:#fff}
th{font-weight:600;color:#37474f;text-align:left;padding:13px 12px;border-bottom:1px solid #e2e5e8;white-space:nowrap}
td{padding:13px 12px;border-top:1px solid #eceff3;vertical-align:top}
tr:nth-child(even) td{background:#fbfcfe}
.text-right{text-align:right}
.badge{display:inline-flex;align-items:center;border-radius:3px;padding:5px 8px;font-size:11px;font-weight:600;white-space:nowrap}
.badge-success{background:#1de9b6;color:#033b31}.badge-secondary{background:#6c757d;color:#fff}.badge-warning{background:#f4c22b;color:#362700}.badge-info{background:#3ebfea;color:#fff}.badge-danger{background:#f44236;color:#fff}.badge-light{background:#eef3f7;color:#535763}.badge-primary{background:#4680ff;color:#fff}
.pagination{display:flex;gap:6px;justify-content:flex-end;margin-top:16px;color:#748892}
.page-dot{border:1px solid #e2e5e8;border-radius:3px;padding:6px 10px;background:#fff}
.page-dot.active{background:#4680ff;color:#fff;border-color:#4680ff}
.list-item{border-bottom:1px solid #eceff3;padding:12px 0}.list-item:last-child{border-bottom:0}
.inline-note{border:1px solid #e2e5e8;border-radius:4px;padding:10px;margin-top:8px;background:#fbfcfe}
.small-textarea{height:54px;border:1px solid #ced4da;border-radius:3px;padding:8px;color:#748892;margin-bottom:8px}
.report-title{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px}
.report-title h2{font-size:18px;margin:0 0 6px;color:#37474f}
.section-title{font-size:14px;font-weight:700;margin:0 0 12px;color:#37474f}
.login-form .field{text-align:left}

/* Wireframe override: black-white mockup, no colored UI states. */
body,.auth-body{background:#fff;color:#111}
.auth-card,.card{background:#fff;border:1.5px solid #111;border-radius:0;box-shadow:none}
.auth-logo,.brand img{filter:grayscale(1) contrast(1.35)}
.auth-title,.page-head h1,.card-header h5,.stat h6,.stat h3,.section-title,.field label{color:#111}
.auth-subtitle,.muted,.breadcrumb,.stat .meta{color:#555}
.sidebar{background:#fff;color:#111;border-right:2px solid #111;box-shadow:none}
.brand{background:#fff;color:#111;border-bottom:2px solid #111}
.caption{color:#111}
.menu a{color:#111;border-bottom:1px solid #d7d7d7}
.menu a.active{background:#fff;color:#111;border-left:6px solid #111;font-weight:700;outline:1px solid #111;outline-offset:-1px}
.menu-icon,.menu a.active .menu-icon{background:#fff;color:#111;border:1px solid #111;border-radius:0}
.topbar{background:#fff;color:#111;border-bottom:2px solid #111}
.profile,.avatar{color:#111}
.avatar{background:#fff;border:1.5px solid #111}
.content{background:#fff}
.card-header{border-bottom:1.5px solid #111}
.card.border,.inline-note{border:1.5px dashed #111;background:#fff;border-radius:0}
.control,.small-textarea{background:#fff;border:1.5px solid #111;border-radius:0;color:#111}
.btn,.btn-primary,.btn-secondary,.btn-info,.btn-success,.btn-warning,.btn-danger,.btn-light{background:#fff!important;color:#111!important;border:1.5px solid #111!important;border-radius:0}
.btn-primary,.btn-success{font-weight:700}
.table-wrap{border:1.5px solid #111;border-radius:0}
table{background:#fff}
th{color:#111;border-bottom:1.5px solid #111}
td{border-top:1px solid #999}
tr:nth-child(even) td{background:#f7f7f7}
.badge,.badge-success,.badge-secondary,.badge-warning,.badge-info,.badge-danger,.badge-light,.badge-primary{background:#fff!important;color:#111!important;border:1px solid #111;border-radius:0}
.badge-success,.badge-primary{font-weight:700}
.pagination{color:#111}
.page-dot{background:#fff;border:1px solid #111;border-radius:0}
.page-dot.active{background:#111;color:#fff;border-color:#111}
.stat-icon{color:#111}
`;

function esc(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

function badge(status) {
    const [label, type] = statusMap[status] ?? [status, 'light'];
    return `<span class="badge badge-${type}">${esc(label)}</span>`;
}

function button(label, type = 'primary', small = true) {
    return `<span class="btn ${small ? 'btn-sm ' : ''}btn-${type}">${esc(label)}</span>`;
}

function actionButtons(items) {
    return `<div class="actions">${items.map(([label, type]) => button(label, type)).join('')}</div>`;
}

function cell(value) {
    if (value && typeof value === 'object' && 'html' in value) {
        return value.html;
    }
    return esc(value);
}

function table(headers, rows) {
    return `
        <div class="table-wrap">
            <table>
                <thead><tr>${headers.map((header) => `<th>${esc(header)}</th>`).join('')}</tr></thead>
                <tbody>
                    ${rows.map((row) => `<tr>${row.map((item) => `<td>${cell(item)}</td>`).join('')}</tr>`).join('')}
                </tbody>
            </table>
        </div>
        <div class="pagination"><span class="page-dot active">1</span><span class="page-dot">2</span><span class="page-dot">3</span></div>
    `;
}

function card(title, body, action = '') {
    return `<div class="card"><div class="card-header"><h5>${esc(title)}</h5>${action}</div><div class="card-body">${body}</div></div>`;
}

function statCards(items) {
    return `<div class="row">${items.map((item) => `
        <div class="col-3">
            <div class="card stat"><div class="card-body">
                <div class="row" style="gap:0">
                    <div class="col-8"><h6>${esc(item.label)}</h6><h3>${esc(item.value)}</h3><div class="meta">${esc(item.meta ?? '')}</div></div>
                    <div class="col-4 stat-icon">${esc(item.icon ?? '')}</div>
                </div>
            </div></div>
        </div>
    `).join('')}</div>`;
}

function field(label, value, span = 6, textarea = false) {
    return `<div class="col-${span} field"><label>${esc(label)}</label><div class="control ${textarea ? 'textarea' : ''}">${esc(value)}</div></div>`;
}

function form(fields) {
    return `<div class="form-row">${fields.join('')}</div><div class="text-right">${button('Batal', 'light', false)} ${button('Simpan', 'primary', false)}</div>`;
}

function filter(items) {
    return `<div class="form-row filter">${items.map((item) => field('', item.value, item.span ?? 3)).join('')}${`<div class="col-2 field"><label>&nbsp;</label>${button('Filter', 'secondary', false).replace('btn-secondary', 'btn-secondary btn-block')}</div>`}</div>`;
}

function appPage({ role, active, title, body }) {
    const user = users[role];
    const menu = menus[role];
    return `<!doctype html><html lang="id"><head><meta charset="utf-8"><title>${esc(title)}</title><style>${css}</style></head>
    <body><div class="layout">
        <aside class="sidebar">
            <div class="brand"><img src="${logoUrl}" alt="Logo"><span>Sistem Eskul</span></div>
            <div class="caption">${roleLabels[role]}</div>
            <nav class="menu">
                ${menu.map((item) => `<a class="${item === active ? 'active' : ''}" href="#"><span class="menu-icon">${esc(item.slice(0, 1))}</span><span>${esc(item)}</span></a>`).join('')}
            </nav>
        </aside>
        <header class="topbar"><div>SMK Yappika Legok</div><div class="profile"><span class="avatar">${user.initials}</span><span>${esc(user.name)}</span></div></header>
        <main class="content">
            <div class="page-head"><h1>${esc(title)}</h1><div class="breadcrumb">Dashboard / ${esc(title)}</div></div>
            ${body}
        </main>
    </div></body></html>`;
}

function loginPage() {
    return `<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Login</title><style>${css}</style></head>
    <body class="auth-body"><div class="auth-card">
        <img class="auth-logo" src="${logoUrl}" alt="Logo">
        <h1 class="auth-title">Sistem Informasi Eskul</h1>
        <p class="auth-subtitle">SMK Yappika Legok</p>
        <div class="login-form">
            ${field('Email', 'admin@smkyappika.sch.id', 12)}
            ${field('Password', '********', 12)}
            <div style="display:flex;align-items:center;gap:8px;margin:6px 0 20px;text-align:left;color:#535763"><span style="width:16px;height:16px;border:1px solid #ced4da;border-radius:2px;display:inline-block"></span> Ingat saya</div>
            <div>${button('Masuk', 'primary', false).replace('btn-primary', 'btn-primary btn-block')}</div>
            <p class="muted" style="margin:22px 0 0">Akun siswa, orang tua, dan pembina dibuat oleh admin.</p>
        </div>
    </div></body></html>`;
}

const data = {
    accounts: [
        ['Admin Utama', 'admin@smkyappika.sch.id', 'ADMIN', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Status', 'warning']]) }],
        ['Budi Santoso', 'budi.pembina@smkyappika.sch.id', 'PEMBINA', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Status', 'warning']]) }],
        ['Rafi Pratama', 'rafi.siswa@smkyappika.sch.id', 'SISWA', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Status', 'warning']]) }],
    ],
    students: [
        ['Rafi Pratama', '23001 / 0067891234', 'XI RPL 1', 'RPL', 'Sari Wulandari', 'rafi.siswa@smkyappika.sch.id', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        ['Nadia Putri', '23002 / 0067891235', 'XI AKL 2', 'AKL', 'Dewi Lestari', 'nadia.siswa@smkyappika.sch.id', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        ['Andi Saputra', '23003 / 0067891236', 'X TKJ 1', 'TKJ', 'Hendra Wijaya', '-', { html: badge('nonaktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
    ],
    parents: [
        ['Sari Wulandari', 'Ibu', '0812-3456-7890', 'sari@email.com', 'sari.ortu@smkyappika.sch.id', 'Rafi Pratama', { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        ['Dewi Lestari', 'Ibu', '0813-1111-2222', 'dewi@email.com', 'dewi.ortu@smkyappika.sch.id', 'Nadia Putri', { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        ['Hendra Wijaya', 'Ayah', '0812-7777-3344', 'hendra@email.com', '-', 'Andi Saputra', { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
    ],
    coaches: [
        ['Budi Santoso', '198801012020121001', '0812-2222-1111', 'budi@email.com', 'budi.pembina@smkyappika.sch.id', 'Futsal', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        ['Maya Kartika', '199002022021012002', '0812-4444-2222', 'maya@email.com', 'maya.pembina@smkyappika.sch.id', 'Paduan Suara', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        ['Agus Rahman', '198705052019031004', '0812-9999-2323', 'agus@email.com', '-', 'Pramuka', { html: badge('nonaktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
    ],
    eskul: [
        [{ html: '<strong>Futsal</strong><div class="muted">Latihan teknik dasar, taktik permainan, dan pembinaan sportivitas.</div>' }, 'Budi Santoso', '30', '24', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        [{ html: '<strong>Paduan Suara</strong><div class="muted">Pengembangan vokal, harmoni, dan persiapan tampil acara sekolah.</div>' }, 'Maya Kartika', '25', '18', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
        [{ html: '<strong>Pramuka</strong><div class="muted">Kedisiplinan, kepemimpinan, dan kegiatan lapangan mingguan.</div>' }, 'Agus Rahman', 'Tanpa batas', '42', { html: badge('nonaktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
    ],
};

function adminPages() {
    return [
        ['admin/dashboard/halaman-dashboard-admin.png', appPage({ role: 'admin', active: 'Dashboard', title: 'Dashboard Admin', body:
            statCards([
                { label: 'Total Siswa', value: '184', icon: 'S' },
                { label: 'Total Pembina', value: '12', icon: 'P' },
                { label: 'Total Orang Tua', value: '156', icon: 'O' },
                { label: 'Total Eskul', value: '9', icon: 'E' },
                { label: 'Pendaftaran Menunggu', value: '18', icon: 'M' },
                { label: 'Anggota Aktif', value: '126', icon: 'A' },
                { label: 'Absensi Menunggu', value: '7', icon: 'H' },
            ]) + card('Pendaftaran Terbaru', table(['Siswa', 'Eskul', 'Pembina', 'Status', 'Tanggal'], [
                ['Rafi Pratama', 'Futsal', 'Budi Santoso', { html: badge('menunggu_validasi_pembina') }, '08 Jul 2026'],
                ['Nadia Putri', 'Paduan Suara', 'Maya Kartika', { html: badge('diterima') }, '07 Jul 2026'],
                ['Andi Saputra', 'Pramuka', 'Agus Rahman', { html: badge('menunggu_validasi_orang_tua') }, '07 Jul 2026'],
            ])),
        })],
        ['admin/kelola-akun/tabel-kelola-akun.png', appPage({ role: 'admin', active: 'Kelola Akun', title: 'Kelola Akun', body: card('Akun Pengguna', filter([{ value: 'Cari nama/email', span: 4 }, { value: 'Semua role', span: 3 }, { value: 'Semua status', span: 3 }]) + table(['Nama', 'Email', 'Role', 'Status', 'Aksi'], data.accounts), button('Tambah Akun', 'primary')) })],
        ['admin/kelola-akun/form-tambah-akun.png', appPage({ role: 'admin', active: 'Kelola Akun', title: 'Tambah Akun', body: card('Tambah Akun', form([field('Nama', 'Budi Santoso'), field('Email', 'budi.pembina@smkyappika.sch.id'), field('Password', '********'), field('Konfirmasi Password', '********'), field('Role', 'Pembina'), field('Status', 'Aktif')])) })],
        ['admin/data-siswa/tabel-data-siswa.png', appPage({ role: 'admin', active: 'Data Siswa', title: 'Data Siswa', body: card('Data Siswa', filter([{ value: 'Cari nama/NIS/NISN', span: 4 }, { value: 'Kelas', span: 2 }, { value: 'Jurusan', span: 2 }, { value: 'Status', span: 2 }]) + table(['Nama', 'NIS/NISN', 'Kelas', 'Jurusan', 'Orang Tua', 'Akun', 'Status', 'Aksi'], data.students), button('Tambah Siswa', 'primary')) })],
        ['admin/data-siswa/form-tambah-siswa.png', appPage({ role: 'admin', active: 'Data Siswa', title: 'Tambah Siswa', body: card('Tambah Siswa', form([field('Akun Siswa', 'Rafi Pratama - rafi.siswa@smkyappika.sch.id'), field('Orang Tua/Wali', 'Sari Wulandari (Ibu)'), field('Nama Siswa', 'Rafi Pratama'), field('NIS', '23001', 3), field('NISN', '0067891234', 3), field('Kelas', 'XI RPL 1', 3), field('Jurusan', 'RPL', 3), field('Jenis Kelamin', 'Laki-laki', 3), field('Tanggal Lahir', '2009-04-12', 3), field('No HP', '0812-5555-6666', 4), field('Email', 'rafi@email.com', 4), field('Status', 'Aktif', 4), field('Alamat', 'Jl. Legok Raya No. 21, Tangerang', 12, true)])) })],
        ['admin/data-orang-tua/tabel-data-orang-tua.png', appPage({ role: 'admin', active: 'Data Orang Tua', title: 'Data Orang Tua', body: card('Data Orang Tua', filter([{ value: 'Cari nama/no HP/email', span: 10 }]) + table(['Nama', 'Hubungan', 'No HP', 'Email', 'Akun', 'Anak', 'Aksi'], data.parents), button('Tambah Orang Tua', 'primary')) })],
        ['admin/data-orang-tua/form-tambah-orang-tua.png', appPage({ role: 'admin', active: 'Data Orang Tua', title: 'Tambah Orang Tua', body: card('Tambah Orang Tua', form([field('Akun Orang Tua', 'Sari Wulandari - sari.ortu@smkyappika.sch.id'), field('Nama', 'Sari Wulandari'), field('No HP', '0812-3456-7890', 4), field('Email', 'sari@email.com', 4), field('Hubungan', 'Ibu', 4), field('Alamat', 'Jl. Legok Raya No. 21, Tangerang', 12, true)])) })],
        ['admin/data-pembina/tabel-data-pembina.png', appPage({ role: 'admin', active: 'Data Pembina', title: 'Data Pembina', body: card('Data Pembina', filter([{ value: 'Cari nama/NIP/no HP', span: 8 }, { value: 'Status', span: 2 }]) + table(['Nama', 'NIP', 'No HP', 'Email', 'Akun', 'Eskul', 'Status', 'Aksi'], data.coaches), button('Tambah Pembina', 'primary')) })],
        ['admin/data-pembina/form-tambah-pembina.png', appPage({ role: 'admin', active: 'Data Pembina', title: 'Tambah Pembina', body: card('Tambah Pembina', form([field('Akun Pembina', 'Budi Santoso - budi.pembina@smkyappika.sch.id'), field('Nama', 'Budi Santoso'), field('NIP', '198801012020121001', 4), field('No HP', '0812-2222-1111', 4), field('Email', 'budi@email.com', 4), field('Status', 'Aktif', 4)])) })],
        ['admin/data-eskul/tabel-data-eskul.png', appPage({ role: 'admin', active: 'Data Eskul', title: 'Data Eskul', body: card('Data Ekstrakurikuler', filter([{ value: 'Cari nama eskul', span: 4 }, { value: 'Semua pembina', span: 3 }, { value: 'Status', span: 3 }]) + table(['Nama Eskul', 'Pembina', 'Kuota', 'Anggota', 'Status', 'Aksi'], data.eskul), button('Tambah Eskul', 'primary')) })],
        ['admin/data-eskul/form-tambah-eskul.png', appPage({ role: 'admin', active: 'Data Eskul', title: 'Tambah Eskul', body: card('Tambah Eskul', form([field('Nama Eskul', 'Futsal'), field('Pembina', 'Budi Santoso'), field('Kuota', '30', 4), field('Status', 'Aktif', 4), field('Deskripsi', 'Latihan teknik dasar, taktik permainan, dan pembinaan sportivitas siswa.', 12, true)])) })],
        ['admin/laporan/laporan-keanggotaan-eskul.png', appPage({ role: 'admin', active: 'Laporan', title: 'Laporan', body:
            statCards([{ label: 'Absensi Disetujui', value: '86', icon: 'D' }, { label: 'Absensi Menunggu', value: '9', icon: 'M' }, { label: 'Absensi Ditolak', value: '3', icon: 'T' }]) +
            card('Laporan Keanggotaan Eskul', filter([{ value: 'Semua siswa', span: 3 }, { value: 'Semua eskul', span: 3 }, { value: 'Semua pembina', span: 2 }, { value: 'Kelas', span: 2 }]) + table(['Siswa', 'Kelas', 'Eskul', 'Pembina', 'Jadwal', 'Absensi', 'Penilaian'], [
                ['Rafi Pratama', 'XI RPL 1', 'Futsal', 'Budi Santoso', { html: 'Senin 15:30 - 17:00<br>Kamis 15:30 - 17:00' }, { html: 'Disetujui: 8<br>Menunggu: 1<br>Ditolak: 0' }, 'Ganjil 2025/2026: 88 (A)'],
                ['Nadia Putri', 'XI AKL 2', 'Paduan Suara', 'Maya Kartika', 'Rabu 14:00 - 16:00', { html: 'Disetujui: 7<br>Menunggu: 0<br>Ditolak: 1' }, 'Ganjil 2025/2026: 91 (A)'],
                ['Andi Saputra', 'X TKJ 1', 'Pramuka', 'Agus Rahman', 'Jumat 14:30 - 16:30', { html: 'Disetujui: 6<br>Menunggu: 2<br>Ditolak: 0' }, '-'],
            ]), button('Cetak', 'secondary')),
        })],
    ];
}

function siswaPages() {
    const eskulCards = `<div class="row">
        ${[
            ['Futsal', 'Latihan teknik dasar, taktik permainan, dan sportivitas.', 'Budi Santoso', '24 / 30', 'belum_daftar'],
            ['Paduan Suara', 'Pengembangan vokal, harmoni, dan persiapan tampil acara sekolah.', 'Maya Kartika', '18 / 25', 'diterima'],
            ['Pramuka', 'Kedisiplinan, kepemimpinan, dan kegiatan lapangan mingguan.', 'Agus Rahman', '42 / Tanpa batas', 'menunggu_validasi_orang_tua'],
        ].map(([name, desc, coach, quota, status]) => `<div class="col-4"><div class="card border"><div class="card-body">
            <div style="display:flex;justify-content:space-between;gap:12px"><h5 style="margin:0 0 12px">${esc(name)}</h5>${badge(status)}</div>
            <p class="muted">${esc(desc)}</p><div>Pembina: ${esc(coach)}</div><div style="margin:8px 0 14px">Anggota: ${esc(quota)}</div>
            <div class="actions" style="justify-content:flex-start">${button('Detail', 'info')}${status === 'belum_daftar' ? button('Daftar', 'primary') : ''}</div>
        </div></div></div>`).join('')}
    </div>`;

    return [
        ['siswa/dashboard/halaman-dashboard-siswa.png', appPage({ role: 'siswa', active: 'Dashboard', title: 'Dashboard Siswa', body:
            statCards([{ label: 'Eskul Diikuti', value: '2', icon: 'E' }, { label: 'Pendaftaran', value: '3', icon: 'P' }, { label: 'Absensi Terakhir', value: '08 Jul', icon: 'A', meta: 'Disetujui' }, { label: 'Nilai Terbaru', value: '88', icon: 'N', meta: 'Predikat A' }]) +
            `<div class="row"><div class="col-6">${card('Status Pendaftaran', `<div class="list-item">Futsal ${badge('menunggu_validasi_pembina')}</div><div class="list-item">Paduan Suara ${badge('diterima')}</div><div class="list-item">Pramuka ${badge('menunggu_validasi_orang_tua')}</div>`)}</div><div class="col-6">${card('Jadwal Eskul', `<div class="list-item"><strong>Futsal</strong><div>Senin 15:30 s/d 17:00 - Lapangan Sekolah</div></div><div class="list-item"><strong>Paduan Suara</strong><div>Rabu 14:00 s/d 16:00 - Aula</div></div>`)}</div></div>`,
        })],
        ['siswa/daftar-eskul/kartu-daftar-eskul.png', appPage({ role: 'siswa', active: 'Daftar Eskul', title: 'Daftar Eskul', body: card('Ekstrakurikuler Aktif', filter([{ value: 'Cari eskul', span: 10 }]) + eskulCards) })],
        ['siswa/daftar-eskul/detail-eskul.png', appPage({ role: 'siswa', active: 'Daftar Eskul', title: 'Detail Eskul', body: `<div class="row"><div class="col-8">${card('Futsal', `<p>Latihan teknik dasar, taktik permainan, dan pembinaan sportivitas siswa.</p><div class="row"><div class="col-4"><strong>Pembina</strong><div>Budi Santoso</div></div><div class="col-4"><strong>Kuota</strong><div>30</div></div><div class="col-4"><strong>Status Pendaftaran</strong><div>${badge('belum_daftar')}</div></div></div>`)}</div><div class="col-4">${card('Jadwal', `<div class="list-item"><strong>Senin</strong><div>15:30 - 17:00</div><div class="muted">Lapangan Sekolah</div></div><div class="list-item"><strong>Kamis</strong><div>15:30 - 17:00</div><div class="muted">Lapangan Sekolah</div></div>`)}</div></div><div class="text-right">${button('Kembali', 'light', false)} ${button('Daftar Eskul', 'primary', false)}</div>` })],
        ['siswa/absensi/tabel-absensi-siswa.png', appPage({ role: 'siswa', active: 'Absensi', title: 'Absensi', body:
            card('Jadwal Eskul Saya', `<h6 class="section-title">Futsal</h6>${table(['Hari', 'Jam', 'Lokasi', 'Status Hari Ini', 'Aksi'], [
                ['Senin', '15:30 - 17:00', 'Lapangan Sekolah', { html: badge('belum_daftar') }, { html: actionButtons([['Absen', 'primary']]) }],
                ['Kamis', '15:30 - 17:00', 'Lapangan Sekolah', { html: badge('menunggu_approval') }, '-'],
            ])}`) +
            card('Riwayat Absensi', table(['Tanggal', 'Eskul', 'Jadwal', 'Status', 'Keterangan'], [
                ['08 Jul 2026', 'Futsal', 'Senin 15:30', { html: badge('disetujui') }, '-'],
                ['01 Jul 2026', 'Paduan Suara', 'Rabu 14:00', { html: badge('menunggu_approval') }, '-'],
                ['24 Jun 2026', 'Futsal', 'Kamis 15:30', { html: badge('ditolak') }, 'Datang terlambat'],
            ])),
        })],
        ['siswa/laporan/laporan-saya.png', appPage({ role: 'siswa', active: 'Laporan Saya', title: 'Laporan Saya', body:
            card('Ringkasan Laporan', `<div class="row"><div class="col-4"><strong>Nama</strong><div>Rafi Pratama</div></div><div class="col-4"><strong>Kelas</strong><div>XI RPL 1</div></div><div class="col-4"><strong>Jurusan</strong><div>RPL</div></div></div><h6 class="section-title" style="margin-top:18px">Keanggotaan Eskul</h6><div class="list-item"><strong>Futsal</strong><div>Pembina: Budi Santoso</div><div>Mulai: 08 Jul 2026</div></div><div class="list-item"><strong>Paduan Suara</strong><div>Pembina: Maya Kartika</div><div>Mulai: 12 Jun 2026</div></div>`, button('Cetak', 'secondary')) +
            `<div class="row"><div class="col-6">${card('Rekap Absensi', `<div class="list-item"><strong>Futsal</strong><div>Disetujui: 8</div><div>Menunggu: 1</div><div>Ditolak: 0</div></div><div class="list-item"><strong>Paduan Suara</strong><div>Disetujui: 7</div><div>Menunggu: 0</div><div>Ditolak: 1</div></div>`)}</div><div class="col-6">${card('Penilaian', `<div class="list-item"><strong>Futsal</strong><div>Ganjil 2025/2026: 88 (A)</div><div class="muted">Aktif dan disiplin saat latihan.</div></div><div class="list-item"><strong>Paduan Suara</strong><div>Ganjil 2025/2026: 91 (A)</div><div class="muted">Vokal stabil dan percaya diri.</div></div>`)}</div></div>`,
        })],
    ];
}

function parentPages() {
    return [
        ['orang-tua/dashboard/halaman-dashboard-orang-tua.png', appPage({ role: 'orang-tua', active: 'Dashboard', title: 'Dashboard Orang Tua', body:
            statCards([{ label: 'Jumlah Anak', value: '2', icon: 'A' }, { label: 'Pendaftaran Anak', value: '4', icon: 'P' }, { label: 'Absensi Anak', value: '18', icon: 'H' }, { label: 'Nilai Anak', value: '5', icon: 'N' }]) +
            card('Ringkasan Anak', table(['Nama', 'Kelas', 'Eskul Aktif', 'Pendaftaran Terakhir', 'Nilai Terbaru'], [
                ['Rafi Pratama', 'XI RPL 1', '2', { html: `Futsal ${badge('menunggu_validasi_orang_tua')}` }, '88 / A'],
                ['Nadia Putri', 'XI AKL 2', '1', { html: `Paduan Suara ${badge('diterima')}` }, '91 / A'],
            ])),
        })],
        ['orang-tua/validasi-pendaftaran/tabel-validasi-pendaftaran.png', appPage({ role: 'orang-tua', active: 'Validasi Pendaftaran', title: 'Validasi Pendaftaran', body:
            card('Pendaftaran Eskul Anak', table(['Anak', 'Eskul', 'Pembina', 'Status', 'Alasan', 'Aksi'], [
                ['Rafi Pratama', 'Futsal', 'Budi Santoso', { html: badge('menunggu_validasi_orang_tua') }, '-', { html: actionButtons([['Approve', 'success'], ['Reject', 'danger']]) + '<div class="inline-note"><div class="small-textarea">Alasan penolakan</div>' + button('Simpan Penolakan', 'danger') + '</div>' }],
                ['Nadia Putri', 'Paduan Suara', 'Maya Kartika', { html: badge('diterima') }, '-', 'Sudah diproses'],
                ['Andi Saputra', 'Pramuka', 'Agus Rahman', { html: badge('ditolak_orang_tua') }, 'Jadwal bentrok les', 'Sudah diproses'],
            ])),
        })],
        ['orang-tua/laporan/laporan-anak.png', appPage({ role: 'orang-tua', active: 'Laporan Anak', title: 'Laporan Anak', body:
            card('Laporan Anak', `<h6 class="section-title">Data Anak</h6><div class="list-item"><strong>Rafi Pratama</strong><div>XI RPL 1 - RPL</div></div><div class="list-item"><strong>Nadia Putri</strong><div>XI AKL 2 - AKL</div></div>`, button('Cetak', 'secondary')) +
            card('Keanggotaan Eskul', table(['Anak', 'Eskul', 'Pembina', 'Jadwal'], [
                ['Rafi Pratama', 'Futsal', 'Budi Santoso', 'Senin 15:30 - 17:00'],
                ['Nadia Putri', 'Paduan Suara', 'Maya Kartika', 'Rabu 14:00 - 16:00'],
            ])) +
            `<div class="row"><div class="col-6">${card('Rekap Absensi', `<div class="list-item"><strong>Rafi Pratama</strong><div>Disetujui: 8</div><div>Menunggu: 1</div><div>Ditolak: 0</div></div><div class="list-item"><strong>Nadia Putri</strong><div>Disetujui: 7</div><div>Menunggu: 0</div><div>Ditolak: 1</div></div>`)}</div><div class="col-6">${card('Penilaian', `<div class="list-item"><strong>Rafi Pratama - Futsal</strong><div>Ganjil 2025/2026: 88 (A)</div><div class="muted">Aktif dan disiplin.</div></div><div class="list-item"><strong>Nadia Putri - Paduan Suara</strong><div>Ganjil 2025/2026: 91 (A)</div><div class="muted">Vokal stabil.</div></div>`)}</div></div>`,
        })],
    ];
}

function coachPages() {
    const reportBody = `<div class="mb-3"><span class="badge badge-light">Periode: 2025/2026</span> <span class="badge badge-light">Semester: Ganjil</span></div>
        <h6 class="section-title">Data Anggota</h6>${table(['Siswa', 'Kelas', 'Eskul', 'Status', 'Jadwal'], [
            ['Rafi Pratama', 'XI RPL 1', 'Futsal', { html: badge('aktif') }, 'Senin 15:30 - 17:00'],
            ['Nadia Putri', 'XI AKL 2', 'Paduan Suara', { html: badge('aktif') }, 'Rabu 14:00 - 16:00'],
        ])}<div class="row"><div class="col-6"><h6 class="section-title">Rekap Absensi</h6><div class="inline-note"><strong>Futsal</strong><div>Disetujui: 18</div><div>Menunggu: 2</div><div>Ditolak: 1</div></div></div><div class="col-6"><h6 class="section-title">Penilaian dan Catatan</h6><div class="inline-note"><strong>Rafi Pratama - Futsal</strong><div>Ganjil 2025/2026: 88 (A)</div><div class="muted">Aktif dan disiplin saat latihan.</div></div></div></div>`;

    return [
        ['pembina/dashboard/halaman-dashboard-pembina.png', appPage({ role: 'pembina', active: 'Dashboard', title: 'Dashboard Pembina', body:
            statCards([{ label: 'Eskul Dibina', value: '2', icon: 'E' }, { label: 'Total Anggota', value: '42', icon: 'A' }, { label: 'Pendaftaran Menunggu', value: '6', icon: 'M' }, { label: 'Absensi Menunggu', value: '7', icon: 'H' }, { label: 'Penilaian', value: '28', icon: 'N' }]) +
            card('Eskul dan Jadwal', `<div class="list-item"><div style="display:flex;justify-content:space-between"><strong>Futsal</strong>${badge('aktif')}</div><span class="badge badge-light">Senin 15:30 - 17:00</span> <span class="badge badge-light">Kamis 15:30 - 17:00</span></div><div class="list-item"><div style="display:flex;justify-content:space-between"><strong>Paduan Suara</strong>${badge('aktif')}</div><span class="badge badge-light">Rabu 14:00 - 16:00</span></div>`),
        })],
        ['pembina/validasi-pendaftaran/tabel-validasi-pendaftaran.png', appPage({ role: 'pembina', active: 'Validasi Pendaftaran', title: 'Validasi Pendaftaran', body:
            card('Pendaftaran Eskul Dibina', filter([{ value: 'Semua status', span: 10 }]) + table(['Siswa', 'Orang Tua', 'Eskul', 'Status', 'Alasan', 'Aksi'], [
                ['Rafi Pratama', 'Sari Wulandari', 'Futsal', { html: badge('menunggu_validasi_pembina') }, '-', { html: actionButtons([['Approve', 'success'], ['Reject', 'danger']]) + '<div class="inline-note"><div class="small-textarea">Alasan penolakan</div>' + button('Simpan Penolakan', 'danger') + '</div>' }],
                ['Nadia Putri', 'Dewi Lestari', 'Paduan Suara', { html: badge('diterima') }, '-', 'Sudah diproses'],
                ['Andi Saputra', 'Hendra Wijaya', 'Pramuka', { html: badge('ditolak_pembina') }, 'Kuota penuh', 'Sudah diproses'],
            ])),
        })],
        ['pembina/jadwal-eskul/tabel-jadwal-eskul.png', appPage({ role: 'pembina', active: 'Jadwal Eskul', title: 'Jadwal Eskul', body:
            card('Jadwal Ekstrakurikuler Dibina', filter([{ value: 'Cari hari/lokasi', span: 4 }, { value: 'Semua eskul binaan', span: 3 }, { value: 'Status', span: 3 }]) + table(['Eskul', 'Hari', 'Jam', 'Lokasi', 'Keterangan', 'Status', 'Aksi'], [
                ['Futsal', 'Senin', '15:30 - 17:00', 'Lapangan Sekolah', 'Latihan teknik dasar', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
                ['Futsal', 'Kamis', '15:30 - 17:00', 'Lapangan Sekolah', 'Sparing internal', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
                ['Paduan Suara', 'Rabu', '14:00 - 16:00', 'Aula', 'Latihan harmoni', { html: badge('aktif') }, { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
            ]), button('Tambah Jadwal', 'primary')),
        })],
        ['pembina/jadwal-eskul/form-tambah-jadwal.png', appPage({ role: 'pembina', active: 'Jadwal Eskul', title: 'Tambah Jadwal', body: card('Tambah Jadwal', form([field('Eskul Binaan', 'Futsal'), field('Hari', 'Senin', 3), field('Status', 'Aktif', 3), field('Jam Mulai', '15:30', 3), field('Jam Selesai', '17:00', 3), field('Lokasi', 'Lapangan Sekolah', 6), field('Keterangan', 'Latihan teknik dasar dan game internal.', 12, true)])) })],
        ['pembina/kelola-absensi/tabel-kelola-absensi.png', appPage({ role: 'pembina', active: 'Kelola Absensi', title: 'Kelola Absensi', body:
            card('Absensi Siswa', filter([{ value: '2026-07-08', span: 2 }, { value: 'Semua eskul', span: 3 }, { value: 'Nama siswa', span: 3 }, { value: 'Status', span: 2 }]) + table(['Tanggal', 'Siswa', 'Eskul', 'Jadwal', 'Status', 'Keterangan', 'Aksi'], [
                ['08 Jul 2026', 'Rafi Pratama', 'Futsal', 'Senin 15:30', { html: badge('menunggu_approval') }, '-', { html: actionButtons([['Setujui', 'success'], ['Tolak', 'danger']]) + '<div class="inline-note"><div class="small-textarea">Keterangan penolakan</div>' + button('Tolak Absensi', 'danger') + '</div>' }],
                ['08 Jul 2026', 'Nadia Putri', 'Paduan Suara', 'Rabu 14:00', { html: badge('disetujui') }, '-', 'Sudah diproses'],
                ['01 Jul 2026', 'Andi Saputra', 'Pramuka', 'Jumat 14:30', { html: badge('ditolak') }, 'Tidak hadir di lokasi', 'Sudah diproses'],
            ])),
        })],
        ['pembina/input-penilaian/tabel-input-penilaian.png', appPage({ role: 'pembina', active: 'Input Penilaian', title: 'Input Penilaian', body:
            card('Penilaian Siswa', filter([{ value: 'Semua eskul', span: 3 }, { value: 'Nama siswa', span: 3 }, { value: 'Periode', span: 3 }]) + table(['Siswa', 'Eskul', 'Periode', 'Semester', 'Nilai', 'Predikat', 'Catatan', 'Aksi'], [
                ['Rafi Pratama', 'Futsal', '2025/2026', 'Ganjil', '88', { html: '<span class="badge badge-primary">A</span>' }, 'Aktif dan disiplin.', { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
                ['Nadia Putri', 'Paduan Suara', '2025/2026', 'Ganjil', '91', { html: '<span class="badge badge-primary">A</span>' }, 'Vokal stabil.', { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
                ['Andi Saputra', 'Pramuka', '2025/2026', 'Ganjil', '78', { html: '<span class="badge badge-primary">B</span>' }, 'Perlu meningkatkan kehadiran.', { html: actionButtons([['Edit', 'info'], ['Hapus', 'danger']]) }],
            ]), button('Input Nilai', 'primary')),
        })],
        ['pembina/input-penilaian/form-input-penilaian.png', appPage({ role: 'pembina', active: 'Input Penilaian', title: 'Input Penilaian', body: card('Input Penilaian', form([field('Anggota Eskul', 'Rafi Pratama - Futsal'), field('Periode', '2025/2026', 3), field('Semester', 'Ganjil', 3), field('Nilai Angka', '88', 3), field('Predikat', 'A', 3), field('Catatan Pembina', 'Aktif, disiplin, dan mampu bekerja sama dalam latihan.', 12, true)])) })],
        ['pembina/laporan/laporan-eskul-dibina.png', appPage({ role: 'pembina', active: 'Buat Laporan', title: 'Buat Laporan', body: card('Laporan Eskul Dibina', filter([{ value: '2025/2026', span: 5 }, { value: 'Ganjil', span: 5 }]) + reportBody, button('Cetak', 'secondary')) })],
        ['pembina/laporan/cetak-laporan-eskul.png', `<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Cetak Laporan Eskul</title><style>${css}.content{margin:0;padding:34px}.print-shell{max-width:1180px;margin:0 auto}.topbar,.sidebar{display:none}</style></head><body><main class="content print-shell"><div class="report-title"><div><h2>Laporan Eskul Dibina</h2><div class="muted">Laporan kegiatan ekstrakurikuler SMK Yappika Legok</div></div>${button('Cetak', 'primary', false)}</div><div class="card"><div class="card-body">${reportBody}</div></div></main></body></html>`],
    ];
}

const pages = [
    ['login/halaman-login.png', loginPage(), { width: 1280, height: 900 }],
    ...adminPages(),
    ...siswaPages(),
    ...parentPages(),
    ...coachPages(),
];

for (const [relativePath, html, size = {}] of pages) {
    const outPath = join(outputRoot, relativePath);
    const htmlPath = join(tempRoot, relativePath.replace(/\.png$/i, '.html'));
    mkdirSync(dirname(outPath), { recursive: true });
    mkdirSync(dirname(htmlPath), { recursive: true });
    writeFileSync(htmlPath, html, 'utf8');

    const width = size.width ?? 1440;
    const height = size.height ?? 1120;
    const result = spawnSync(chromePath, [
        '--headless=new',
        '--disable-gpu',
        '--no-sandbox',
        '--allow-file-access-from-files',
        `--window-size=${width},${height}`,
        `--screenshot=${outPath}`,
        pathToFileURL(htmlPath).href,
    ], { encoding: 'utf8' });

    if (result.status !== 0) {
        console.error(`Gagal membuat ${relativePath}`);
        console.error(result.stderr || result.stdout);
        process.exit(result.status ?? 1);
    }
}

console.log(`Berhasil membuat ${pages.length} mockup PNG di ${outputRoot}`);
