$(function () {
    $('.js-basic-example').DataTable({
        responsive: true,
        aaSorting : [[0,'desc']]
    });


    //Exportable table
    $('.js-exportable').DataTable({
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});