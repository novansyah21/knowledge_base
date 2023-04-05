<!-- Icons Grid-->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
  
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>

<section class="features-icons bg-light text-center">
    <div class="container">
    <h5 style="text-align: left;">Plafond : <span class="badge bg-primary">Rp. <?php  echo number_format($plafond,2,',','.'); ?></span></h5>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <figure class="highcharts-figure">
                            <div id="container-gf">
                                
                            </div>
                        </figure>
                        <br>
                        <br>
                        <br>
                        <!-- Datatable Qty Recomendation -->
                        <table id="myTable" class="display">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Product Name</th>
                                    <th style="text-align: center;">Price / pcs</th>
                                    <th style="text-align: center;">Qty Recomendation</th>
                                    <!-- <th style="text-align: center;">Distance</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach($hasil['hasil'] as $data){ ?>
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $data['product_name']; ?></td>
                                        <td><?php echo number_format($data['price_pcs'],2,',','.'); ?></td>
                                        <td><?php echo $data['qty_recom']; ?></td>
                                        <!-- <td><?php echo $data['distance']; ?></td> -->
                                    </tr>
                                <?php 
                                $no++;
                              } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <br><br><br>
        <h4 style="text-align: left;"><span class="badge bg-primary">Before</span></h4>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <br>
                        <!-- Data Asli yang di upload -->
                        <table id="myTable2" class="display">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Product Name</th>
                                    <th style="text-align: center;">Price / pcs</th>
                                    <th style="text-align: center;">Qty IN</th>
                                    <th style="text-align: center;">Qty OUT</th>
                                    <th style="text-align: center;background-color: #cfd4d0;">PERSENTASE (%)</th>
                                    <th style="text-align: center;background-color: #ffc6c2;">KREDIT</th>
                                    <th style="text-align: center;background-color: #c2ffd0;">DEBIT</th>
                                    <th style="text-align: center;background-color: #cfd4d0;">SISA</th>
                                    <!-- <th style="text-align: center;">Distance</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <?php print_r($data_upload); ?> -->
                                <?php 
                                $no = 1;
                                $COLOR_SISA = '';
                                $TOTAL_KREDIT = 0;
                                $TOTAL_DEBIT = 0;
                                $TOTAL_SISA = 0;
                                $PERSENTASE = [];

                                foreach($data_upload as $data){ ?>

                                    <?php 

                                        $PERSENTASE[$no] =  number_format((float)($data['quantity_out'] / $data['quantity_in']) * 100, 2, '.', ''); // Menghitung persentase dari barang yang terjual dibanding dengan stock awal Produk[0],[1] .....
                                        $KREDIT = $data['price'] * $data['quantity_in']; // Total Modal Stock Produk[0],[1] .....
                                        $DEBIT = $data['price'] * $data['quantity_out']; // Total Keuntungan Produk[0],[1] .....
                                        $SISA = $DEBIT - $KREDIT; // Keuntungan - Modal Stock Produk[0],[1] .....

                                        // Pewarnaan
                                        if($SISA < 0){
                                            $COLOR_SISA = '#ffc6c2';
                                        } else if($SISA == 0){
                                            $COLOR_SISA = '';
                                        } else {
                                            $COLOR_SISA = '#c2ffd0';
                                        }

                                        $TOTAL_KREDIT = $TOTAL_KREDIT + $KREDIT; // Total Keseluruhan Modal Stock 
                                        $TOTAL_DEBIT = $TOTAL_DEBIT + $DEBIT; // Total Keseluruhan Keuntungan
                                        $TOTAL_SISA = $TOTAL_SISA + $SISA; // Sisa dari Total Keseluruhan Modal - Keuntungan

                                    ?>

                                    <!-- Print Tampilan Data Asli -->
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $data['product_name']; ?></td>
                                        <td><?php echo number_format($data['price'],2,',','.'); ?></td>
                                        <td><?php echo $data['quantity_in']; ?></td>
                                        <td><?php echo $data['quantity_out']; ?></td>
                                        <td style="text-align: center;background-color: #f7f3b7"><?php echo $PERSENTASE[$no]; ?></td>
                                        <td style="text-align: right;background-color: #ffc6c2;">
                                            <?php echo number_format($KREDIT,2,',','.'); ?>
                                        </td>
                                        <td style="text-align: right;background-color: #c2ffd0;">
                                            <?php echo number_format($DEBIT,2,',','.'); ?>
                                        </td>
                                        <td style="text-align: right;background-color: <?php echo $COLOR_SISA; ?>;">
                                            <?php echo number_format($SISA,2,',','.'); ?>
                                        </td>
                                    </tr>
                                <?php 
                                $no++;
                              } ?>
                            </tbody>
                            <tfoot>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: center;background-color: #dbdbdb;"><b>GRAND TOTAL</b></td>
                                <td style="text-align: right;background-color: #ffc6c2;"><b><?php echo number_format($TOTAL_KREDIT,2,',','.'); ?></b></td>
                                <td style="text-align: right;background-color: #c2ffd0;"><b><?php echo number_format($TOTAL_DEBIT,2,',','.'); ?></b></td>
                                <td style="text-align: right;background-color: #f7f3b7"><b><?php echo number_format($TOTAL_SISA,2,',','.'); ?></b></td>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <br><br><br>
        <h4 style="text-align: left;"><span class="badge bg-primary">After</span></h4>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <br>
                        <!-- Data Setelah menggunakan Algoritma -->
                        <table id="myTable3" class="display">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Product Name</th>
                                    <th style="text-align: center;">Price / pcs</th>
                                    <th style="text-align: center;">Qty IN</th>
                                    <th style="text-align: center;background-color: #cfd4d0;">PERSENTASE (%)</th>
                                    <th style="text-align: center;">Qty OUT</th>
                                    <th style="text-align: center;background-color: #ffc6c2;">KREDIT</th>
                                    <th style="text-align: center;background-color: #c2ffd0;">DEBIT</th>
                                    <th style="text-align: center;background-color: #cfd4d0;">SISA</th>
                                    <!-- <th style="text-align: center;">Distance</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $COLOR_SISA = '';
                                $TOTAL_KREDIT = 0;
                                $TOTAL_DEBIT = 0;
                                $TOTAL_SISA = 0;
                                
                                // Data yang tampil disesuaikan dengan data, rekomendasi dari sistem
                                foreach($hasil['hasil'] as $data){ ?>

                                    <?php 
                                        $KREDIT = $data['price_pcs'] * $data['qty_recom']; // Modal Awal Produk[0],[1], ....
                                        $QTY_OUT_AFTER = round(($data['qty_recom'] * $PERSENTASE[$no]) / 100); // Stock keluar jika persentase penjualan sama
                                        $DEBIT = $data['price_pcs'] * $QTY_OUT_AFTER; // Keuntungan produk[0],[1], .....
                                        $SISA = $DEBIT - $KREDIT;  // Sisa Keuntungan - Modal awal produk[0],[1], .....

                                        if($SISA < 0){
                                            $COLOR_SISA = '#ffc6c2';
                                        } else if($SISA == 0){
                                            $COLOR_SISA = '';
                                        } else {
                                            $COLOR_SISA = '#c2ffd0';
                                        }

                                        $TOTAL_KREDIT = $TOTAL_KREDIT + $KREDIT;
                                        $TOTAL_DEBIT = $TOTAL_DEBIT + $DEBIT;
                                        $TOTAL_SISA = $TOTAL_SISA + $SISA;
                                    ?>
                                    <!-- Print tampilan setelah penerapan algoritma -->
                                    <tr>
                                        <td><?php echo $no; ?></td>
                                        <td><?php echo $data['product_name']; ?></td>
                                        <td><?php echo number_format($data['price_pcs'],2,',','.'); ?></td>
                                        <td><?php echo $data['qty_recom']; ?></td>
                                        <td style="text-align: center;background-color: #f7f3b7"><?php echo $PERSENTASE[$no]; ?></td>
                                        <td><?php echo $QTY_OUT_AFTER; ?></td>
                                        <td style="text-align: right;background-color: #ffc6c2;">
                                            <?php echo number_format($KREDIT,2,',','.'); ?>
                                        </td>
                                        <td style="text-align: right;background-color: #c2ffd0;">
                                            <?php echo number_format($DEBIT,2,',','.'); ?>
                                        </td>
                                        <td style="text-align: right;background-color: <?php echo $COLOR_SISA; ?>;">
                                            <?php echo number_format($SISA,2,',','.'); ?>
                                        </td>
                                    </tr>
                                <?php 
                                $no++;
                              } ?>
                            </tbody>
                            <tfoot>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: center;background-color: #dbdbdb;"><b>GRAND TOTAL</b></td>
                                <td style="text-align: right;background-color: #ffc6c2;"><b><?php echo number_format($TOTAL_KREDIT,2,',','.'); ?></b></td>
                                <td style="text-align: right;background-color: #c2ffd0;"><b><?php echo number_format($TOTAL_DEBIT,2,',','.'); ?></b></td>
                                <td style="text-align: right;background-color: #f7f3b7"><b><?php echo number_format($TOTAL_SISA,2,',','.'); ?></b></td>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- Call to Action-->

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
Highcharts.chart('container-gf', {
    chart: {
        type: 'scatter',
        zoomType: 'xy'
    },
    title: {
        text: 'Grafik Cluster Kmeans'
    },
    subtitle: {
        text: 'Source: UT 2022'
    },
    xAxis: {
        title: {
            enabled: true,
            text: 'Euclidian Distance'
        },
        startOnTick: true,
        endOnTick: true,
        showLastLabel: true
    },
    yAxis: {
        title: {
            text: 'Cluster'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        verticalAlign: 'top',
        x: 100,
        y: 70,
        floating: true,
        backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
        borderWidth: 1
    },
    plotOptions: {
        scatter: {
            marker: {
                radius: 5,
                states: {
                    hover: {
                        enabled: true,
                        lineColor: 'rgb(100,100,100)'
                    }
                }
            },
            states: {
                hover: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b><br>',
                pointFormat: '{point.x}, {point.y}'
            }
        }
    },
    series: [
      <?php 
        foreach($data_cluster as $data){
          $random_red = rand(1,255);
          $random_green = rand(1,255);
          $random_blue = rand(1,255);
          $rgb = $random_red.", ".$random_green.", ".$random_blue.", .5";

          echo "{ ";
          echo "name: '".$data['product_name']."',";
          echo "color: 'rgba(".$rgb.")',";
          echo "data: [[".$data['euclidian_distance'].", ".$data['k']."]]";
          echo " }, ";
        }
      ?>
    ]
    // series: [{
    //     name: 'Cutter',
    //     color: 'rgba(223, 83, 83, .5)',
    //     data: [[52.5, 2.0]]

    // }, {
    //     name: 'Pisau',
    //     color: 'rgba(119, 152, 191, .5)',
    //     data: [[21.0, 4.0]]
    // }, {
    //     name: 'Keyboard',
    //     color: 'rgba(169, 152, 191, .5)',
    //     data: [[86.4, 4.0]]
    // }, {
    //     name: 'Mouse',
    //     color: 'rgba(219, 152, 191, .5)',
    //     data: [[21.0, 2.0]]
    // }]
});

</script>

<script>

    $(document).ready( function () {
        $('#myTable').DataTable();
    } );

    $(document).ready( function () {
        $('#myTable2').DataTable();
    } );

    $(document).ready( function () {
        $('#myTable3').DataTable();
    } );
    

</script>
        
