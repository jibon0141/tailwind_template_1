
@if($data)
    <!-- css plugging start -->
    @section('app_styles')

        @if(in_array('summernote',$data))
            <!-- summernote -->
            <link rel="stylesheet" href="{{asset('assets/backend_assets/plugins/summernote/summernote-bs4.min.css')}}">
            <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        @endif
        @if(in_array('dashboard_plugin',$data))








        @endif
        @if(in_array('data_table',$data))
            <!-- DataTables -->
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
            <link rel="stylesheet" href="{{asset('assets/backend_assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
            <link rel="stylesheet" href="{{asset('assets/backend_assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
            <link rel="stylesheet" href="{{asset('assets/backend_assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

        @endif
    @endsection
    <!-- css plugin end -->
    <!-- js plugins start -->
    @section('app_scripts')
        @if(in_array('summernote',$data))
            <!-- Summernote -->

            <script src="{{asset('assets/backend_assets/plugins/summernote/summernote-bs4.min.js')}}"></script>

        @endif
        @if(in_array('data_table',$data))
            <!-- DataTables  & Plugins -->
            <script src="{{asset('assets/backend_assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/jszip/jszip.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/pdfmake/pdfmake.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/pdfmake/vfs_fonts.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
            <script src="{{asset('assets/backend_assets/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
            <script>
                jQuery.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
                    console.log(message);
                    console.log(helpPage);
                    console.log(settings);
                };
                jQuery.extend(true, $.fn.dataTable.defaults, {
                    paging: true,
                    pageLength: 10,
                    processing: true,
                    serverSide: true,
                    bStateSave: true,
                    search: {
                        "regex": true
                    },
                    searching: {
                        "regex": true
                    },
                    lengthMenu: [
                        [5,10,25, 50, 100, -1],
                        [5,10,25, 50, 100, "All"]
                    ],
                    language: {
                        paginate: {next: '&#62;', previous: '&#60;'},
                        emptyTable: "<i class='d-block my-4 fas fa-box-open' style='font-size: 120px'></i>",
                        processing: "<i class='fas fa-spinner fa-pulse' style='font-size: 80px'></i>",
                    },
                    oLanguage: {
                        sZeroRecords: "<i class='d-block my-4 fas fa-box-open' style='font-size: 120px'></i>"
                    },
                });

            </script>
        @endif

        @if(in_array('validation_jquery',$data))
            <script src="{{ asset('assets/backend_assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
            <script src="{{ asset('assets/backend_assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
        @endif

        @if(in_array('dashboard_plugin',$data))

        @endif
    @endsection
    <!-- js plugin end -->
@endif
