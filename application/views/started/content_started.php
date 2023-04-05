<!-- Icons Grid-->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>


<section class="features-icons bg-light text-center">
    <form method="post" action="<?php echo base_url('started/UploadReport');?>" enctype="multipart/form-data">
        <div class="container">
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row mt-2 d-flex justify-content-center">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-5">
                                    <input type="Name" class="form-control" name="name" id="name" placeholder="Sebutkan nama Anda ...">
                                </div>
                            </div>
                            <div class="form-group row mt-2 d-flex justify-content-center">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Usia</label>
                                <div class="col-sm-5">
                                    <input type="Name" class="form-control" name="name" id="name" placeholder="Berapa usia Anda saat ini ?">
                                </div>
                            </div>
                            <div class="form-group row mt-2 d-flex justify-content-center">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Ganguan Otot</label>
                                <div class="col-sm-5">
                                    <select class="form-select" aria-label="Default select example" id="jenis_otot">
                                        <option value="atas">Atas</option>
                                        <option value="tangan">Tangan</option>
                                        <option value="tengah">Tengah</option>
                                        <option value="kaki">Kaki</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary btn-sm">Next</button>
                        <!-- </form> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<!-- Call to Action-->

<script>

    function DownloadTemplate(){

        $.ajax({
        type: 'GET',
        url: '<?php echo base_url(); ?>Started/DownloadTemplate',
        data: {
        },
        dataType: 'json',
        error: function() {
            Swal.fire(
                'Failed!',
                'Your Download Failed!',
                'error'
            )
        },
        success: function() {
            Swal.fire(
            'Done!',
            'Your Download Success!',
            'success'
            )
        }
        });
    }

    $(document).ready( function () {
        $('#myTable').DataTable();
    } );

    console.log()
    

</script>
        
