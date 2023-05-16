<?php
include('db.php');
//include('functions.php');
//include('header.php');

$db = new Database();

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Verificação de ações requisitadas via AJAX:
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
if (isset($_GET["a"])) {

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Buscar conteúdo na div conteudo:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "graph_vendas") {

        $data_de = $_POST['dDe'];
        $data_ate = $_POST['dAte'];
 
        $res = $db->select("SELECT concat(pro_name,' Tamanho ' , pro_tamanho) as pei_name, sum(pei_qtd) as qtd_venda FROM tb_itens_pedido
                            INNER JOIN tb_produtos ON pro_id = pei_idprod
                            ORDER BY qtd_venda DESC");
        echo json_encode($res);
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Buscar conteúdo na div conteudo:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "graph_estoque") {

        $data_de = $_POST['dDe'];
        $data_ate = $_POST['dAte'];
        
        $res = $db->select("SELECT concat(pro_name,' Tamanho ' , pro_tamanho) as pro_name, pro_quantidade as qtd_est FROM tb_produtos
                            ORDER BY pro_quantidade DESC");
        echo json_encode($res);
    }

    die();
}

// Includes para o script:
include('header.php');
include('aside.php');
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Dashboard</h6>
                        </div>
                    </div>
                    
                    <div class="card-body px-0 pb-2">
                        <div class="form-group row" style="padding-left:15px;">
                            <div class="col-6">
                                <div class="input-group input-group-outline">
                                    <input type="date" class="form-control data" style="text-align: center; height: 38px; font-size: 11pt !important" placeholder="" value="<?php echo date("Ymd", strtotime("-1 month")); ?>" name="frmdatade" id="frmdatade">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group">
                                    <input type="date" class="form-control data" style="text-align: center; height: 38px; font-size: 11pt !important" placeholder="" value="<?php echo date("Ymd"); ?>" name="frmdatate" id="frmdatate">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive p-0" id="div_conteudo">
                            <div class="graph-container">
                                <div class="col">
                                    <h2 class="text-center pb-5">Vendas</h2>
                                    <canvas id="myChart1"></canvas>
                                </div>
                                <div class="col">
                                    <h2 class="text-center pb-5">Estoque</h2>
                                    <canvas id="myChart3"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("footer.php"); ?>

<style>
	.graph-container {
		margin-top: 50px;
		display: grid;
		grid-template-columns: repeat(1, 1fr);
		grid-gap: 100px;
	}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
	var marketplace = '<?= $_SESSION["marketplaceCo"] ?>';
	var ctx1 = document.getElementById('myChart1').getContext('2d');
	var ctx3 = document.getElementById('myChart3').getContext('2d');

	$(document).ready(function() {
		atuaChart();
	});

	function atuaChart() {

		$.ajax({
			url: '?a=graph_vendas',
			type: 'POST',
			data: {
				dDe: $("#frm_datade").val(),
				dAte: $("#frm_datate").val()
			},
			success: function(data) {
				var dt = JSON.parse(data)
                labels3 = [];
				valores3 = [];

				for (var i = 0; i < dt.length; i++) {
					labels3.push(dt[i].pei_name);
					valores3.push(dt[i].qtd_venda);
				}

				window.myChart3 = new Chart(ctx1, {
					type: 'bar',
					data: {
						datasets: [{
							data: valores3,
							backgroundColor: ['#730217', '#D94A64', '#8090BF', '#400101','#181926', '#D7D7D9', '#A64684', '#E3BE02', '#0371FC', '#C36003', '#FC8AF0']
						}],
						labels: labels3
					}
				});

			},
			error: function(err) {
				$(".pedidos").html('Erro ao carregar indicador!');
			}
		});

		$.ajax({
			url: '?a=graph_estoque',
			type: 'POST',
			data: {
				dDe: $("#frm_datade").val(),
				dAte: $("#frm_datate").val()
			},
			success: function(data) {
                var dt = JSON.parse(data)
				labels4 = [];
				valores4 = [];
				for (var i = 0; i < dt.length; i++) {
					labels4.push(dt[i].pro_name);
					valores4.push(dt[i].qtd_est);
				}
				window.myChart4 = new Chart(ctx3, {
					type: 'bar',
                
					data: {
						datasets: [{
							data: valores4,
							backgroundColor: ['#008080', '#4682B4', '#7B68EE', '#ADD8E6', '#4FC276', '#699A97', '#695597', '#E3BE02', '#0371FC', '#C36003', '#FC8AF0']
						}],
						labels: labels4
					}
				});

			},
			error: function(err) {
				$(".pedidos").html('Erro ao carregar indicador!');
			}
		});

	}

	function destroyChart() {
		window.myChart1.destroy();
		window.myChart3.destroy();
		window.myChart4.destroy();
	}
</script>