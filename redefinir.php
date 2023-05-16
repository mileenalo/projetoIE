<?php 

include("db.php");

$db = new Database();

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
* Verificação de ações requisitadas via AJAX:
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
if (isset($_GET["a"])) {

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	* Edita conteúdo:
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	if ($_GET["a"] == "logar") {

		$email = $_POST["email"];
		$senha = md5($_POST["senha"]);

		if($email == "" || $senha == ""){
			echo '<div class="alert alert-warning" role="alert">';
				echo 'O campo email ou senha se encontra vazio!';
			echo '</div>';
		}else{

            $upd = $db->_exec("UPDATE tb_usuarios SET usu_password = '{$senha}' WHERE usu_email = '{$email}'");
            
            echo $upd;

		}
	}

	die();
}

include("header.php");
?>

<body class="bg-gray-200">
  	<main class="main-content  mt-0">
   		<div class="page-header align-items-start min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');">
      		<span class="mask bg-gradient-dark opacity-6"></span>
      		<div class="container my-auto">
        		<div class="row">
          			<div class="col-lg-4 col-md-8 col-12 mx-auto">
            			<div class="card z-index-0 fadeIn3 fadeInBottom">
              				<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                				<div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                  					<h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Redefinir a Senha</h4>
                				</div>
              				</div>
              			<div class="card-body">
                			<form role="form" class="text-start">
                  				<div class="input-group input-group-outline my-3">
                    				<label class="form-label">Email</label>
                    				<input type="email" class="form-control" id="frm_email">
                  				</div>
                  				<div class="input-group input-group-outline mb-3">
                    				<label class="form-label">Nova Senha</label>
                    				<input type="password" class="form-control" id="frm_password">
                  				</div>
                  				<div class="text-center">
                    				<button type="button" class="btn bg-gradient-primary w-100 my-4 mb-2" onclick="logar();">Entrar</button>
                  				</div>
                			</form>
              			</div>
           	 		</div>
          		</div>
        	</div>
      	</div>
  	</main>
</body>

<?php 
	include("bottom.php");
?>
<script>
	var win = navigator.platform.indexOf('Win') > -1;
	if (win && document.querySelector('#sidenav-scrollbar')) {
		var options = {
			damping: '0.5'
		}
		Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Valida o login:
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	var ajax_div = $.ajax(null);
	const logar = () => {
		if (ajax_div) {
			ajax_div.abort();
		}
		ajax_div = $.ajax({
			cache: false,
			async: true,
			url: '?a=logar',
			type: 'post',
			data: {
				email: $("#frm_email").val(),
				senha: $("#frm_password").val(),
			},
			success: function retorno_ajax(retorno) {
				console.log(retorno);
				if(retorno == 1){
					document.location.href="./login.php";
				}else{
					if (retorno) {
						$('#div_retorno').html(retorno);
					} else {
						alert("ERRO! " + retorno);
					}
				}
			}
		});
	}
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="./assets/js/material-dashboard.min.js?v=3.0.4"></script>
