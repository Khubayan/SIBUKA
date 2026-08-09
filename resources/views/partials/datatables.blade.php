@push('styles')
<link href="{{ asset('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('assets/sb-admin-2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/sb-admin-2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            }
        });
    });
</script>
@endpush
