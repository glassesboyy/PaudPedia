import { useSchoolStore } from '@/stores/school.store'

type ModuleId = 'class' | 'student' | 'parent' | 'teacher' | 'operator' | 'attendance' | 'dashboard' | 'spp' | 'savings' | 'assessment' | 'report'

interface PageCopy {
  title: string
  subtitle: string
}

export function usePageCopy() {
  const schoolStore = useSchoolStore()
  const role = schoolStore.currentRole

  const getCopy = (moduleId: ModuleId): PageCopy => {
    switch (moduleId) {
      case 'operator':
        return {
          title: 'Manajemen Operator',
          subtitle: 'Kelola tim operator yang bertanggung jawab atas operasional harian sekolah'
        }

      case 'class':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Kelas',
            subtitle: 'Pantau data ruangan kelas dan wali kelas di sekolah Anda (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Manajemen Kelas',
            subtitle: 'Kelola data ruangan kelas dan wali kelas di sekolah Anda'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Daftar Kelas',
            subtitle: 'Lihat daftar kelas yang Anda ampu beserta detailnya'
          }
        }
        return {
          title: 'Daftar Kelas',
          subtitle: 'Lihat daftar kelas dan informasi wali kelas di sekolah'
        }

      case 'student':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Siswa',
            subtitle: 'Pantau data peserta didik yang terdaftar di sekolah Anda (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Manajemen Siswa',
            subtitle: 'Kelola data peserta didik di sekolah Anda'
          }
        }
        if (role === 'parent') {
          return {
            title: 'Anak Saya',
            subtitle: 'Informasi lengkap mengenai perkembangan dan data anak Anda'
          }
        }
        return {
          title: 'Data Siswa',
          subtitle: 'Lihat informasi lengkap peserta didik di sekolah'
        }

      case 'parent':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Wali Murid',
            subtitle: 'Pantau data wali murid yang terdaftar di sekolah Anda (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Manajemen Wali Murid',
            subtitle: 'Kelola data wali murid yang terdaftar di sekolah Anda'
          }
        }
        return {
          title: 'Data Wali Murid',
          subtitle: 'Lihat informasi wali murid yang terdaftar di sekolah'
        }

      case 'teacher':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Guru',
            subtitle: 'Pantau daftar dan jadwal pengajaran guru di sekolah Anda (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Manajemen Guru',
            subtitle: 'Kelola daftar dan informasi guru di sekolah'
          }
        }
        return {
          title: 'Data Guru',
          subtitle: 'Lihat daftar dan informasi guru di sekolah'
        }

      case 'attendance':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Presensi',
            subtitle: 'Pantau rekapitulasi kehadiran seluruh siswa di sekolah Anda (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Rekap Presensi',
            subtitle: 'Lihat dan kelola rekapitulasi kehadiran seluruh siswa di sekolah'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Presensi Kelas',
            subtitle: 'Input pencatatan kehadiran harian siswa kelas yang Anda ampu'
          }
        }
        return {
          title: 'Informasi Presensi',
          subtitle: 'Lihat rekapitulasi kehadiran siswa di sekolah'
        }

      case 'spp':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring SPP',
            subtitle: 'Pantau laporan tagihan dan penerimaan SPP bulanan sekolah (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Manajemen SPP',
            subtitle: 'Kelola tagihan massal dan verifikasi pembayaran SPP siswa'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Tagihan SPP Kelas',
            subtitle: 'Pantau tagihan dan catat pembayaran SPP siswa kelas Anda'
          }
        }
        return {
          title: 'Informasi SPP',
          subtitle: 'Daftar tagihan dan pembayaran SPP'
        }

      case 'savings':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Tabungan',
            subtitle: 'Pantau transaksi setoran dan penarikan tabungan siswa (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Manajemen Tabungan',
            subtitle: 'Kelola pencatatan transaksi setoran dan penarikan tabungan siswa'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Tabungan Siswa Kelas',
            subtitle: 'Catat transaksi setoran dan penarikan tabungan siswa kelas Anda'
          }
        }
        return {
          title: 'Tabungan Siswa',
          subtitle: 'Informasi rekapitulasi tabungan siswa'
        }

      case 'assessment':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Penilaian',
            subtitle: 'Pantau rekapitulasi penilaian perkembangan bulanan anak (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Rekap Penilaian',
            subtitle: 'Lihat rekapitulasi penilaian skala perkembangan anak di sekolah'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Input Penilaian',
            subtitle: 'Input pencatatan perkembangan anak secara periodik (Bulanan)'
          }
        }
        return {
          title: 'Penilaian Perkembangan',
          subtitle: 'Informasi skala perkembangan anak di sekolah'
        }

      case 'report':
        if (role === 'headmaster') {
          return {
            title: 'Monitoring Rapor',
            subtitle: 'Pantau rekapitulasi penyusunan dan pengunduhan rapor siswa (Read-Only)'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Cetak Rapor Siswa',
            subtitle: 'Unduh dan cetak berkas PDF rapor perkembangan peserta didik'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Rapor Siswa Kelas',
            subtitle: 'Susun narasi dan unduh berkas PDF rapor peserta didik kelas Anda'
          }
        }
        return {
          title: 'Laporan / Rapor Siswa',
          subtitle: 'Unduh rapor perkembangan peserta didik'
        }

      case 'dashboard':
        if (role === 'headmaster') {
          return {
            title: 'Panel Eksekutif',
            subtitle: 'Ringkasan pengawasan eksekutif dan statistik global sekolah Anda'
          }
        }
        if (role === 'parent') {
          return {
            title: 'Portal Orang Tua',
            subtitle: 'Pantau aktivitas dan data akademik buah hati Anda'
          }
        }
        if (role === 'teacher') {
          return {
            title: 'Portal Guru',
            subtitle: 'Ringkasan kelas dan aktivitas pengajaran Anda'
          }
        }
        if (role === 'operator') {
          return {
            title: 'Panel Operasional',
            subtitle: 'Kelola administrasi dan kegiatan operasional harian sekolah'
          }
        }
        return {
          title: 'Panel Manajemen',
          subtitle: 'Ringkasan informasi dan aktivitas di sekolah Anda'
        }

      default:
        return {
          title: 'SIAKAD',
          subtitle: 'Sistem Informasi Akademik'
        }
    }
  }

  return { getCopy }
}
