define([
    'app',
    'datatables.net',
    'datatables.net-bs4',
    // 'datatables.net-autofill',
    // 'datatables.net-autofill-bs',
    'datatables.net-buttons',
    'datatables.net-buttons-bs',
    'datatables.net-buttons.colVis',
    'datatables.net-buttons.flash',
    'datatables.net-buttons.html5',
    'datatables.net-buttons.print',
    // 'datatables.net-colreorder',
    'datatables.net-fixedcolumns',
    'datatables.net-fixedheader',
    // 'datatables.net-keytable',
    'datatables.net-responsive',
    'datatables.net-responsive-bs',
    'datatables.net-rowgroup',
    'datatables.net-scroller',
    'datatables.net-select',
    // 'datatables.net-piMarkjs'
], function(app) {
    app.directive('jqDataTables', [
        '$timeout',
        'blockUI',
        function($timeout, BlockUI) {
            return {
                restrict: 'A',
                scope: {
                    options: '=',
                    rowbtn: '=',
                    blocker: '=',
                },
                link: function(scope, elem, attrs) {

                    var _timer = function() {
                        if (!angular.isUndefined(scope.options)) {
                            var table = $('#' + elem[0].id).DataTable({
                                "dom": angular.isUndefined(scope.options.dom) ? 'lfrtip' : scope.options.dom,
                                "paging": angular.isUndefined(scope.options.paging) ? false : scope.options.paging,
                                "lengthChange": angular.isUndefined(scope.options.lengthChange) ? true : scope.options.lengthChange,
                                "lengthMenu": angular.isUndefined(scope.options.lengthMenu) ? [] : scope.options.lengthMenu,
                                "pageLength": angular.isUndefined(scope.options.pageLength) ? 25 : scope.options.pageLength,
                                "searching": angular.isUndefined(scope.options.searching) ? true : scope.options.searching,
                                "ordering": angular.isUndefined(scope.options.ordering) ? true : scope.options.ordering,
                                "info": angular.isUndefined(scope.options.info) ? true : scope.options.info,
                                "autoWidth": angular.isUndefined(scope.options.autoWidth) ? true : scope.options.autoWidth,
                                "select": angular.isUndefined(scope.options.select) ? false : scope.options.select,
                                "keys": angular.isUndefined(scope.options.keys) ? false : scope.options.keys,
                                "mark": angular.isUndefined(scope.options.mark) ? false : scope.options.mark,
                                "responsive": angular.isUndefined(scope.options.responsive) ? undefined : scope.options.responsive,
                                "processing": angular.isUndefined(scope.options.processing) ? false : scope.options.processing,
                                "serverSide": angular.isUndefined(scope.options.serverSide) ? false : scope.options.serverSide,
                                "ajax": angular.isUndefined(scope.options.ajax) ? '' : scope.options.ajax,
                                "language": angular.isUndefined(scope.options.language) ? {} : scope.options.language,
                                "buttons": angular.isUndefined(scope.options.buttons) ? [] : scope.options.buttons,
                                "data": angular.isUndefined(scope.options.data) ? [] : scope.options.data,
                                "columnDefs": angular.isUndefined(scope.options.columnDefs) ? [] : scope.options.columnDefs,
                                "order": angular.isUndefined(scope.options.order) ? [] : scope.options.order,
                                "columns": angular.isUndefined(scope.options.columns) ? [] : scope.options.columns,
                                "footerCallback": angular.isUndefined(scope.options.footerCallback) ? function(row, data, start, end, display) {} : scope.options.footerCallback,
                                "fnRowCallback": angular.isUndefined(scope.options.fnRowCallback) ? function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {} : scope.options.fnRowCallback,
                                "createdRow": angular.isUndefined(scope.options.createdRow) ? function(row, data, dataIndex) {} : scope.options.createdRow
                            });



                            /**
                             * Selecting config
                             */
                            var _selectConfig = function() {
                                /**
                                 * Handle event when cell gains focus
                                 */
                                $('#' + elem[0].id).on('key-focus.dt', function(e, datatable, cell) {
                                    // Select highlighted row
                                    table.row(cell.index().row).select();
                                });

                                /**
                                 * Handle click on table cell
                                 */
                                // $('#' + elem[0].id).on('click', 'tbody td', function(e){
                                //     e.stopPropagation();

                                //     // Get index of the clicked row
                                //     var rowIdx = table.cell(this).index().row;

                                //     // Select row
                                //     table.row(rowIdx).select();
                                // });
                                $('#' + elem[0].id).on('click', 'tbody>tr', function(e) {
                                    e.stopPropagation();
                                    if ($(this).hasClass('child')) {} else {
                                        if ($(this).hasClass('selected')) {
                                            // $(this).removeClass('selected');
                                        } else {
                                            // var rowIdx = table.cell(this).index().row;
                                            // table.row(rowIdx).select();
                                            $('#' + elem[0].id).DataTable().$('tr.selected').removeClass('selected');
                                            $(this).addClass('selected');
                                        }
                                    }
                                });

                                /**
                                 * Handle key event that hasn't been handled by KeyTable
                                 */
                                $('#' + elem[0].id).on('key.dt', function(e, datatable, key, cell, originalEvent) {
                                    // If ENTER key is pressed
                                    if (key === 13) {
                                        // Get highlighted row data
                                        var data = table.row(cell.index().row).data();
                                    }
                                });

                                /**
                                 * Handle custom button when selecting row
                                 */
                                table.on('select deselect', function() {
                                    console.log('Selected')
                                    var selectedRows = table.rows({ selected: true }).count();
                                    var customBtn = table.buttons(['.edit', '.delete']);

                                    (selectedRows === 1) ? customBtn.enable(): customBtn.disable();
                                });
                            };

                            // Handle click on "Select all" control
                            $('#dt-select-all').on('click', function() {
                                // Get all rows with search applied
                                var rows = table.rows({ 'search': 'applied' }).nodes();
                                // Check/uncheck checkboxes for all rows in the table
                                $('input[type="checkbox"]', rows).prop('checked', this.checked);
                                $('tbody>tr').addClass('selected');
                            });

                            // Handle click on checkbox to set state of "Select all" control
                            $('#' + elem[0].id).on('change', 'input[type="checkbox"]', function() {
                                // If checkbox is not checked
                                if (!this.checked) {
                                    var el = $('#dt-select-all').get(0);
                                    // If "Select all" control is checked and has 'indeterminate' property
                                    if (el && el.checked && ('indeterminate' in el)) {
                                        // Set visual state of "Select all" control
                                        // as 'indeterminate'
                                        el.indeterminate = true;
                                    }
                                }
                            });

                            /**
                             * Configuration of row
                             */
                            var _rowConfig = function() {
                                /**
                                 * Use to implement the index value per row.
                                 */
                                // table.on( 'order.dt search.dt', function () {
                                //     table.column(0, {search:'applied', ordering:'applied'}).nodes().each( function (cell, i) {
                                //         cell.innerHTML = i+1;
                                //     } );
                                // } ).draw();

                                table.on('draw.dt search.dt', function() {
                                    var info = table.page.info();
                                    table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function(cell, i) {
                                        cell.innerHTML = i + 1 + info.start;
                                    });
                                }).draw();

                                /**
                                 * Use to detect if the button in the row is clicked
                                 */
                                $('#' + elem[0].id).on('click', 'tr button', function(e) {
                                    var data = table.row($(this).parents('tr')).data();
                                    var index = table.row($(this).parents('tr')).index();

                                    data = (data == undefined) ? table.row($(this).parents('tr').prev('tr')).data() : data;
                                    index = (index == undefined) ? table.row($(this).parents('tr').prev('tr')).index() : index;

                                    if ($(this)[0].id == 'firstButton') {
                                        scope.rowbtn.firstButton(data, index)
                                    } else if ($(this)[0].id == 'secondButton') {
                                        scope.rowbtn.secondButton(data, index)
                                    } else if ($(this)[0].id == 'thirdButton') {
                                        scope.rowbtn.thirdButton(data, index)
                                    } else if ($(this)[0].id == 'fourthButton') {
                                        scope.rowbtn.fourthButton(data, index)
                                    } else if ($(this)[0].id == 'fifthButton') {
                                        scope.rowbtn.fifthButton(data, index)
                                    } else if ($(this)[0].id == 'signatoriesErfButton') {
                                        var erf_no = $(this)[0].name;
                                        scope.rowbtn.signatoriesErfButton(data, erf_no);
                                    } else {
                                        console.error('Please put some id in button, like firstButton:secondButton:thirdButton');
                                    }
                                });
                            }

                            /**
                             * initialize configuration
                             */
                            var _initConfig = function() {
                                /**
                                 * Focus datatable search bar.
                                 */
                                $('#' + elem[0].id + '_filter>label>input').focus();

                                _selectConfig();
                                _rowConfig();

                                BlockUI.instances.get(scope.blocker).stop();
                            };

                            _initConfig();
                        } else {
                            $timeout(_timer, 500);
                        }
                    }

                    $timeout(_timer, 500);
                }
            };
        }
    ]);
}); // end define