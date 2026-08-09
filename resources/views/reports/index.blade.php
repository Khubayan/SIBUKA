@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<x-page-heading title="Laporan Buku Kasus" />

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Generate Laporan</h6>
    </div>
    <div class="card-body">
        <form id="reportForm" method="GET" action="">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="report_type">Jenis Laporan <span class="text-danger">*</span></label>
                    <select name="report_type" id="report_type" class="form-control" required>
                        <option value="student">Per Siswa</option>
                        <option value="class">Per Kelas</option>
                        @if($canSchoolReport)
                            <option value="school">Sekolah</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="format">Format <span class="text-danger">*</span></label>
                    <select name="format" id="format" class="form-control" required>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV (Excel)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="date_from">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="date_to">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-4 mb-3 field-student">
                    <label for="student_id">Siswa <span class="text-danger">*</span></label>
                    <select name="student_id" id="student_id" class="form-control">
                        <option value="">Pilih siswa...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->nama }} ({{ $student->kelas?->nama }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3 field-class d-none">
                    <label for="kelas_id">Kelas <span class="text-danger">*</span></label>
                    <select name="kelas_id" id="kelas_id" class="form-control">
                        <option value="">Pilih kelas...</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama }} ({{ $kelas->jurusan?->kode }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row field-filter">
                <div class="col-md-4 mb-3 field-kategori">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" class="form-control">
                        <option value="">Semua</option>
                        <option value="pelanggaran">Pelanggaran</option>
                        <option value="prestasi">Prestasi</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3 field-school d-none">
                    <label for="jurusan_id">Jurusan</label>
                    <select name="jurusan_id" id="jurusan_id" class="form-control">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id }}">{{ $jurusan->kode }} — {{ $jurusan->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3 field-school d-none">
                    <label for="tingkat">Tingkat</label>
                    <select name="tingkat" id="tingkat" class="form-control">
                        <option value="">Semua Tingkat</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-download"></i> Download Laporan
            </button>
        </form>

        <small class="text-muted d-block mt-3">
            Hanya kasus berstatus <strong>confirmed</strong> yang dimasukkan dalam laporan.
            CSV dapat dibuka di Microsoft Excel.
        </small>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reportForm');
    const reportType = document.getElementById('report_type');
    const studentField = document.querySelector('.field-student');
    const classField = document.querySelector('.field-class');
    const schoolFields = document.querySelectorAll('.field-school');
    const kategoriField = document.querySelector('.field-kategori');

    const routes = {
        student: @json(route('reports.student', ['student' => '__ID__'])),
        class: @json(route('reports.class', ['kelas' => '__ID__'])),
        school: @json(route('reports.school')),
    };

    function toggleFields() {
        const type = reportType.value;

        studentField.classList.toggle('d-none', type !== 'student');
        classField.classList.toggle('d-none', type !== 'class');
        schoolFields.forEach(el => el.classList.toggle('d-none', type !== 'school'));
        kategoriField.classList.toggle('d-none', type === 'student');
    }

    reportType.addEventListener('change', toggleFields);
    toggleFields();

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const type = reportType.value;
        const params = new URLSearchParams();
        const format = document.getElementById('format').value;
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;

        params.set('format', format);
        if (dateFrom) params.set('date_from', dateFrom);
        if (dateTo) params.set('date_to', dateTo);

        let url = '';

        if (type === 'student') {
            const studentId = document.getElementById('student_id').value;
            if (!studentId) { alert('Pilih siswa terlebih dahulu.'); return; }
            url = routes.student.replace('__ID__', studentId);
        } else if (type === 'class') {
            const kelasId = document.getElementById('kelas_id').value;
            if (!kelasId) { alert('Pilih kelas terlebih dahulu.'); return; }
            url = routes.class.replace('__ID__', kelasId);
            const kategori = document.getElementById('kategori').value;
            if (kategori) params.set('kategori', kategori);
        } else {
            url = routes.school;
            const kategori = document.getElementById('kategori').value;
            const jurusanId = document.getElementById('jurusan_id').value;
            const tingkat = document.getElementById('tingkat').value;
            if (kategori) params.set('kategori', kategori);
            if (jurusanId) params.set('jurusan_id', jurusanId);
            if (tingkat) params.set('tingkat', tingkat);
        }

        window.location.href = url + '?' + params.toString();
    });
});
</script>
@endpush
