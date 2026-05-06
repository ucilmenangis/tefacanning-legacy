# Sprint Retrospective — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:**
| Role | Name |
|------|------|
| Project Manager | Rizky |
| System Analyst | Lily |
| UI/UX (Figma) | Alfia |
| Frontend | Alif Taran Ihsan |
| Backend | Irfan |

**Period:** 25 Februari 2026 – 6 Mei 2026 (Sprint 1–7)
**Framework:** Scrum (adaptasi untuk tim 5 orang)

---

## Sprint 1 — Project Setup & Database

### What went well
- Backend berhasil setup repositori, koneksi database, dan dokumentasi dasar dalam 2 hari
- Project kickoff meeting berjalan lancar, scope dan timeline jelas
- SRS document draft selesai, kebutuhan stakeholder terdokumentasi
- Design system Figma terdefinisi sejak awal (colors, typography, spacing)

### What could be improved
- Gap 7 minggu antara Sprint 1 dan Sprint 2 karena jadwal kuliah — progress terhenti
- Frontend developer belum mulai kerja di sprint ini
- Konfigurasi database membutuhkan sinkronisasi manual dengan Laravel project

### Action items
- Komunikasi jadwal kuliah ke seluruh tim agar sprint planning bisa disesuaikan
- Pastikan semua anggota tim punya akses database sebelum sprint dimulai

---

## Sprint 2 — Landing Page & Core Infrastructure

### What went well
- Backend menyelesaikan landing page + core infrastructure + auth system dalam 1 hari intensif
- CLAUDE.md documentation membantu onboard Alif tanpa verbal communication panjang
- Figma mockup landing page selesai dan digunakan sebagai referensi
- Use case diagram dan analisis sistem memberikan arah jelas untuk development

### What could be improved
- Backend mengerjakan terlalu banyak task sendirian (9 dari 10 commits)
- Frontend hanya 1 commit — porsi kerja tidak seimbang
- Bridge ke stakeholder untuk konten landing page bisa lebih awal

### Action items
- Distribusi task lebih merata di sprint berikutnya
- Frontend mulai active sprint 3 dengan lebih banyak task

---

## Sprint 3 — Customer Panel Frontend & Backend

### What went well
- Frontend produktif — 4 commits, customer dashboard, riwayat pesanan, profile, admin panel
- Backend wiring berjalan paralel dengan frontend — sinkronisasi efektif
- Peran PM dan SA membantu koordinasi antara frontend dan backend
- Figma mockups customer & admin panel selesai tepat waktu

### What could be improved
- DB connection null issue menyita waktu debugging
- Konversi HTML ke PHP memakan waktu — bisa dicegah jika langsung PHP dari awal
- Komunikasi perubahan schema ke frontend kurang timely

### Action items
- Selalu gunakan `.php` extension dari awal (bukan `.html` lalu convert)
- Backend perlu komunikasi aktif ketika ada perubahan schema database

---

## Sprint 4 — Tailwind Migration & Admin Wiring

### What went well
- Tailwind migration berhasil — CSS native berkurang drastis, konsistensi UI meningkat
- RBAC system solid — super_admin vs teknisi dengan price & deletion protection
- Admin panel wiring 7 halaman ke DB selesai dalam 1 sprint
- Dokumentasi keamanan (RBAC) membantu tim memahami role system

### What could be improved
- Windows path issue pada sidebar menyita waktu — environment difference antar developer
- Logout fix diperlukan di multiple pages — menandakan inconsistent auth handling
- Frontend hanya 1 task (edit customer/order page)

### Action items
- Standardisasi development environment (path handling, PHP version)
- Tambahkan logout handling di shared layout, bukan per-page

---

## Sprint 5 — PDF, CRUD Pages & Bug Fixes

### What went well
- PDF generation (DomPDF) berhasil diimplementasi — porting dari Laravel template
- 4 halaman CRUD admin baru selesai (create order, edit product, view order, edit batch)
- 15 commits — sprint paling produktif
- Figma PDF template design selesai tepat waktu untuk referensi

### What could be improved
- Multiple UI bug fixes menandakan kurangnya testing sebelum commit
- Dummy chart sebelumnya — seharusnya real data dari awal
- Frontend hanya 1 commit (optimization) — bisa lebih banyak kontribusi

### Action items
- Implementasi real data dari awal, bukan placeholder/dummy
- Tambahkan basic testing sebelum commit (manual smoke test)

---

## Sprint 6 — OOP Refactoring

### What went well
- OOP refactoring massive berhasil — Database singleton, BaseService, 9 service classes, SessionGuard interface, guards, Auth facade, CsrfService, FlashMessage, FormatHelper, exception hierarchy
- Design spec documentasi membantu sebagai blueprint sebelum coding
- Code maintainability meningkat signifikan
- Review arsitektur OOP oleh SA memberikan validasi

### What could be improved
- Refactoring besar dalam 1 sprint — risiko tinggi, banyak halaman broken
- Post-refactor bugs (product, batch, customer cannot be accessed) — testing tidak cukup menyeluruh
- Frontend dan UI/UX tidak terlalu terlibat di sprint ini (sprint backend-heavy)

### Action items
- Untuk refactoring besar, pertimbangkan phased approach (bukan big bang)
- Tambahkan smoke test checklist setelah refactoring

---

## Sprint 7 — OOP Completion, Bug Fixes & Retrospective

### What went well
- OrderAdminService class, stock management, batch filter, FonnteService (WhatsApp) — fitur penting selesai
- Merge conflict dari Alif berhasil di-resolve
- Retrospective dan sprint review terdokumentasi
- Laporan final project dan slide presentasi selesai oleh PM
- Dokumentasi sistem lengkap (SRS, DFD, ERD) oleh SA

### What could be improved
- Alif's merge menyebabkan semua admin page error (u.phone column) — root cause: migration belum dijalankan
- create-customer.php mengirim data ke wrong table (users vs customers) — logic error
- Merge conflict bisa dicegah dengan komunikasi lebih baik sebelum merge

### Action items
- **WAJIB** `git pull` dan `php artisan migrate` sebelum mulai coding
- Konsisten gunakan conventional commit format (`feat:`, `fix:`, `refactor:`)
- Code review sebelum merge ke main branch

---

## Overall Retrospective Summary

### What went well
1. **Role separation jelas** — PM, SA, UI/UX, Frontend, Backend masing-masing punya tanggung jawab yang terdefinisi
2. **CLAUDE.md documentation** — membantu onboard anggota tim baru tanpa verbal communication panjang
3. **OOP refactoring** — meningkatkan code maintainability secara signifikan
4. **Git commit history** — 66 commits well-documented, progress traceable
5. **Figma design system** — konsisten dari awal sampai akhir, referensi UI jelas
6. **Communication tools** — penggunaan dokumentasi (CLAUDE.md, docs/) sebagai single source of truth
7. **Scrum framework** — adaptasi untuk tim 5 orang berjalan efektif

### What could be improved
1. **Gap 7 minggu** — antara Sprint 1 dan 2 karena jadwal kuliah, bisa di-mitigate dengan async work
2. **Merge conflict pada Sprint 7** — bisa dicegah dengan komunikasi lebih baik
3. **Missing migration** — Alif perlu menjalankan `php artisan migrate` sebelum mulai coding
4. **Commit format inconsistency** — beberapa commit tidak mengikuti conventional commit format
5. **Testing** — tidak ada automated testing, semua manual. Sprint 8 diperlukan untuk full testing
6. **Task distribution** — beberapa sprint backend-heavy (Irfan 57 dari 66 commits)
7. **Stakeholder communication** — bisa lebih frekuen di luar sprint cycle

### Action items for future projects
1. Selalu `git pull` & `php artisan migrate` sebelum mulai coding
2. Konsisten gunakan conventional commit format (`feat:`, `fix:`, `refactor:`, `docs:`)
3. Komunikasi aktif ketika ada perubahan schema database
4. Sinkronisasi antar role (PM, SA, UI/UX, dev) lebih sering di luar sprint
5. Code review wajib sebelum merge ke main
6. Implementasi automated testing (unit test, integration test)
7. Phased refactoring approach untuk perubahan besar
8. Standardisasi development environment (PHP version, extensions, path handling)

### Team Reflections

**Rizky (PM):** "Project management berjalan baik meskipun ada gap jadwal. Sprint planning dan review membantu tim tetap on-track. Ke depan, komunikasi antar sprint bisa lebih sering."

**Lily (SA):** "Dokumentasi sistem dan bridge ke stakeholder membantu tim memahami requirement. Analisis data transaksi membantu validasi fitur. Full testing di Sprint 8 penting untuk memastikan semua requirement terpenuhi."

**Alfia (UI/UX):** "Figma design system konsisten dari awal membantu developer implementasi UI. Dark mode variants dan PDF template design penting untuk konsistensi visual. UI consistency audit di sprint 6 membantu menemukan inconsistency."

**Alif (Frontend):** "10 commits selama 7 sprint — bisa lebih produktif. CLAUDE.md sangat membantu onboard tanpa perlu banyak tanya. Pelajaran: selalu pull & migrate sebelum coding."

**Irfan (Backend):** "57 commits — backend-heavy memang karena wiring, auth, RBAC, OOP refactoring. OOP refactoring investasi besar tapi hasilnya worth it. Pelajaran: phased refactoring lebih aman dari big bang."
