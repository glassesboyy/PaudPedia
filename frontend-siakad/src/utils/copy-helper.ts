import { useSchoolStore } from '@/stores/school.store'

type ModuleId = 'class' | 'student' | 'parent' | 'teacher' | 'attendance' | 'dashboard'

interface PageCopy {
  title: string
  subtitle: string
}

export function usePageCopy() {
  const schoolStore = useSchoolStore()
  const role = schoolStore.currentRole

  const getCopy = (moduleId: ModuleId): PageCopy => {
    switch (moduleId) {
      case 'class':
        if (role === 'headmaster') {
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
            title: 'Kelola Siswa',
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
            title: 'Kelola Orang Tua',
            subtitle: 'Kelola data wali murid yang terdaftar di sekolah Anda'
          }
        }
        return {
          title: 'Data Orang Tua',
          subtitle: 'Lihat informasi wali murid yang terdaftar di sekolah'
        }

      case 'teacher':
        return {
          title: 'Data Guru',
          subtitle: 'Lihat daftar dan informasi tenaga pendidik di sekolah'
        }

      case 'attendance':
        if (role === 'headmaster') {
          return {
            title: 'Kelola Absensi',
            subtitle: 'Pantau dan kelola kehadiran seluruh siswa di sekolah'
          }
        }
        return {
          title: 'Informasi Absensi',
          subtitle: 'Lihat rekapaulasi kehadiran siswa di sekolah'
        }

      case 'dashboard':
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
