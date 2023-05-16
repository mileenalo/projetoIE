<?php

include('db.php');
//include('functions.php');
//include('header.php');

$db = new Database();

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Verificação de ações requisitadas via AJAX:
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
if (isset($_GET["a"])) {

    function formataCPFouCNPJ($cpf){
        if ($cpf) {
            $cpf = trim($cpf);
            $cpf = str_replace(".", "", $cpf);
            $cpf = str_replace("-", "", $cpf);
            $cpf = str_replace("/", "", $cpf);
    
            if (strlen($cpf) == 11) {
                return substr($cpf, 0, 3) . "." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-" . substr($cpf, 9, 2);
            } else {
                return substr($cpf, 0, 2) . "." . substr($cpf, 2, 3) . "." . substr($cpf, 5, 3) . "/" . substr($cpf, 8, 4) . "-" . substr($cpf, 12, 2);
            }
        } else {
            return '';
        }
	}

    function remove_acento($string)
    {
        $caracteres_sem_acento = array(
            'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Â' => 'Z', 'Â' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ñ' => 'N', 'Å' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
            'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'Å' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
            'Ä' => 'a', 'î' => 'i', 'â' => 'a', 'È' => 's', 'È' => 't', 'Ä' => 'A', 'Î' => 'I', 'Â' => 'A', 'È' => 'S', 'È' => 'T',
        );
        $nova_string = strtr($string, $caracteres_sem_acento);
        return ($nova_string);
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
        * Buscar conteúdo na div conteudo:
        * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "lista_client") {

        $pesquisa = $_POST['pesq'];
        $where = "";

        if ($pesquisa != "") {
            $where .= "WHERE cli_name LIKE '%{$pesquisa}%' OR cli_cpf LIKE '%{$pesquisa}%' OR cli_email LIKE '%{$pesquisa}%'";
        }

        $res = $db->select("SELECT * FROM tb_clientes
                                {$where} ORDER BY cli_name");

        if (count($res) > 0) {
            echo '<table class="table align-items-center mb-0">';
            echo '  <thead>';
            echo '      <tr>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">Usuário</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Telefone</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">CPF</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Data Nascimento</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">E-mail</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Editar</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Deletar</th>';
            echo '      </tr>';
            echo '  </thead>';
            echo '  <tbody>';
            foreach($res as $r){
                echo '<tr>';
                echo '  <td class="align-middle text-left">';
                echo '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">'.$r["cli_name"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">('.substr($r["cli_telefone"], 0, 2).')'.substr($r["cli_telefone"], 2, 5).'-'.substr($r["cli_telefone"], 7, 4).'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.formataCPFouCNPJ($r["cli_cpf"]).'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.substr($r["cli_dtnasc"], 6, 2). '/' .substr($r["cli_dtnasc"], 4, 2). '/' .substr($r["cli_dtnasc"], 0, 4).'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.$r["cli_email"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle">';
                echo '      <i title="Editar" onclick="get_item(\'' . $r["cli_id"] . '\')" class="fa fa-edit" style="cursor: pointer"></i>';
                echo '  </td>';
                echo '  <td class="align-middle">';
                echo '      <i title="Deletar" onclick="del_item(\'' . $r["cli_id"] . '\')" class="fa fa-trash" style="cursor: pointer"></i>';
                echo '  </td>';
                echo '</tr>';
            }
            echo '  </tbody>';
            echo '</table>';
        } else{
            echo '<div class="alert alert-warning" role="alert" style="margin-left: 15px;margin-right:25px;">';
            echo 'Nenhum registro localizado!';
            echo '</div>';
        }
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Inserir conteúdo dentro da lista de pedidos criada em lista_mod_insert:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "inclui_client") {

        $name = $_POST["name"];
        $email = $_POST["email"];
        $telefone = str_replace("(", "", $_POST["telefone"]);
        $telefone = str_replace(")", "", $telefone);
        $telefone = str_replace("-", "", $telefone);
        $telefone = str_replace(" ", "", $telefone);

        $cpf = str_replace(".", "", $_POST["cpf"]);
        $cpf = str_replace("-", "", $cpf);
        $cpf = str_replace("/", "", $cpf);

        $dataNasc = str_replace("/", "", $_POST["dataNasc"]);
        $dataNasc = substr($dataNasc, 4, 4) . substr($dataNasc, 2, 2) . substr($dataNasc, 0, 2);
        
        $res = $db->_exec("INSERT INTO tb_clientes (cli_name, cli_email, cli_telefone, cli_cpf, cli_dtnasc) 
                            VALUES ('{$name}','{$email}','{$telefone}', '{$cpf}', '{$dataNasc}')");

        echo $res;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * editar conteúdo dentro da lista de pedidos do modal de edição:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "edita_client") {

        $id = $_POST["id"];
        $name = $_POST["name"];
        $email = $_POST["email"];
        $telefone = $_POST["telefone"];
        $cpf = $_POST["cpf"];
        $dataNasc = $_POST["dataNasc"];
       
        $telefone = str_replace("(", "", $_POST["telefone"]);
        $telefone = str_replace(")", "", $telefone);
        $telefone = str_replace("-", "", $telefone);
        $telefone = str_replace(" ", "", $telefone);

        $cpf = str_replace(".", "", $_POST["cpf"]);
        $cpf = str_replace("-", "", $cpf);
        $cpf = str_replace("/", "", $cpf);

        $dataNasc = str_replace("/", "", $_POST["dataNasc"]);
        $dataNasc = substr($dataNasc, 4, 4) . substr($dataNasc, 2, 2) . substr($dataNasc, 0, 2);

        $res = $db->_exec("UPDATE tb_clientes 
                            SET cli_name = '{$name}', cli_email = '{$email}', 
                            cli_telefone = '{$telefone}', cli_cpf = '{$cpf}', 
                            cli_dtnasc = '{$dataNasc}' 
                        WHERE cli_id = $id");

        echo $res;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Deleta o pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "del_client") {

        $id = $_POST["id"];

        $del = $db->_exec("DELETE FROM tb_clientes WHERE cli_id = {$id}");

        echo $del;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Busca conteúdo para exibir na div de edição do pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "get_client") {


        $id = $_POST["id"];

        $res = $db->select("SELECT * FROM tb_clientes 
                            WHERE cli_id = {$id}");

        if (count($res) > 0) {
            $res[0]['cli_name'] = utf8_decode($res[0]['cli_name']);
            $res[0]['cli_dtnasc'] = substr($res[0]["cli_dtnasc"], 6, 2). '/' .substr($res[0]["cli_dtnasc"], 4, 2). '/' .substr($res[0]["cli_dtnasc"], 0, 4);
            $res[0]['cli_cpf'] = formataCPFouCNPJ($res[0]["cli_cpf"]);
            $res[0]['cli_telefone'] = '(' . substr($res[0]["cli_telefone"], 0, 2) . ')' . substr($res[0]["cli_telefone"], 2, 5) . '-' . substr($res[0]["cli_telefone"], 7, 4);
            
        }

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
                            <h6 class="text-white text-capitalize ps-3">Cadastro de Documentos</h6>
                        </div>
                    </div>
                    
                    <div class="card-body px-0 pb-2">
                        <div class="form-group row" style="padding-left:15px;">
                            <div class="col-10">
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" onkeyup="lista_itens()" id="input_pesquisa" placeholder="Pesquisar">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <button type="button" onclick="$('#mod_formul').modal('show');" class="btn bg-gradient-primary" style="height: 38px"><i class="mdi mdi-library-plus" style="margin-right: 5px"></i>Incluir</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive p-0" id="div_conteudo"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </main>

    <!-- Modal formulário Inclusao -->
    <div class="modal" id="mod_formul">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" style="max-width: 50%;">
            <div class="modal-content">
                <div class="modal-header" style="align-items: center">
                    <div style="display: flex; align-items: center">
                        <div style="margin-right: 5px">
                            <h2 style="margin: 0"><span class="badge bg-info text-white" style="padding: 8px" id="span_endereco_nome"></span></h2>
                        </div>
                        <div>
                            <h5 id="tit_frm_formul" class="modal-title">Incluir Documento</h5>
                        </div>
                    </div>
                    <button type="button" style="cursor: pointer; border: 1px solid #ccc; border-radius: 10px" aria-label="Fechar" onclick="$('#mod_formul').modal('hide');">X</button>
                </div>
                <div class="modal-body modal-dialog-scrollable">
                    <form id="frm_general" name="frm_general" class="col">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Nome:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_nome" placeholder="Ex: Maria Luiza">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val2_insert" class="form-label">CPF/CNPJ:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_cpf" placeholder="Ex: XXX.XXX.XXX-XX">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Telefone:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_telefone" placeholder="(XX)XXXXX-XXXX">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val2_insert" class="form-label">Data nascimento:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_dtNasc" placeholder="Ex: dd/mm/aaaa">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="frm_val1_insert" class="form-label">Email:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_email" placeholder="Ex: teste@gmail.com">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#mod_formul').modal('hide');">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="OK" onclick="incluiCli();"><img id="img_btn_ok" style="width: 15px; display: none; margin-right: 10px">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal formulário Edição-->

    <div class="modal" id="mod_formul_edit">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" style="max-width: 50%;">
            <div class="modal-content">
                <div class="modal-header" style="align-items: center">
                    <div style="display: flex; align-items: center">
                        <div style="margin-right: 5px">
                            <h2 style="margin: 0"><span class="badge bg-info text-white" style="padding: 8px" id="span_endereco_nome"></span></h2>
                        </div>
                        <div>
                            <h5 id="div_edit_title">Editar Cliente</h5>
                        </div>
                    </div>
                    <button type="button" style="cursor: pointer; border: 1px solid #ccc; border-radius: 10px" aria-label="Fechar" onclick="location.reload();">X</button>
                </div>
                <div class="modal-body modal-dialog-scrollable">
                <form id="frm_general" name="frm_general" class="col">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Nome:</label>
                                <div class="input-group input-group-outline">
                                    <input id="frm_id_edit" hidden>
                                    <input type="text" class="form-control" id="frm_nome_edit" placeholder="Ex: Maria Luiza">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val2_insert" class="form-label">CPF/CNPJ:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_cpf_edit" placeholder="Ex: XXX.XXX.XXX-XX">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Telefone:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_telefone_edit" placeholder="(XX)XXXXX-XXXX">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val2_insert" class="form-label">Data nascimento:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_dtNasc_edit" placeholder="Ex: dd/mm/aaaa">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="frm_val1_insert" class="form-label">Email:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_email_edit" placeholder="Ex: teste@gmail.com">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="location.reload();">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="frm_OK" onclick="editUsu();"><img id="img_btn_ok" style="width: 15px; display: none; margin-right: 10px">OK</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
<script type="text/javascript">
   
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Listar itens:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const lista_itens = () => {
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?a=lista_client',
            type: 'post',
            data: {
                pesq: $('#input_pesquisa').val()
            },
            beforeSend: function() {
                $('#div_conteudo').html('<div class="spinner-grow m-3 text-primary" role="status"><span class="visually-hidden">Aguarde...</span></div>');
            },
            success: function retorno_ajax(retorno) {
                //console.log(retorno);
                $('#div_conteudo').html(retorno);
            }
        });
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * inclui no modal os itens para inclusão:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const incluiCli = () => {
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?a=inclui_client',
            type: 'post',
            data: {
                name: $("#frm_nome").val(),
                email: $("#frm_email").val(),
                cpf: $("#frm_cpf").val(),
                dataNasc: $("#frm_dtNasc").val(),
                telefone: $("#frm_telefone").val(),
            },
            success: function retorno_ajax(retorno) {
                console.log(retorno)
                if (!retorno) {
                    alert("ERRO AO INLUIR USUÁRIO!");
                }else{
                    $("#mod_formul").modal('hide');
                    lista_itens();
                }
            }
        });
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * permite a edição de itens dentro do pedido:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const editUsu = (countarray, iditens, idPed) => {

        if (confirm("Confirma a edição do cliente?")) {
            if (ajax_div) {
                ajax_div.abort();
            }

            ajax_div = $.ajax({
                cache: false,
                async: true,
                url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=edita_client',
                type: 'post',
                data: {
                    name: $("#frm_nome_edit").val(),
                    email: $("#frm_email_edit").val(),
                    cpf: $("#frm_cpf_edit").val(),
                    dataNasc: $("#frm_dtNasc_edit").val(),
                    telefone: $("#frm_telefone_edit").val(),
                    id: $('#frm_id_edit').val(),
                },
                beforeSend: function() {
                    $('#mod_formul_edit').modal("show");
                },
                success: function retorno_ajax(retorno) {
                    if (!retorno) {
                        alert("ERRO AO EDITAR O USUÁRIO!");
                    }else{
                        $('#mod_formul_edit').modal("hide");
                        lista_itens();
                    }
                }
            });
        }
    }

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Pesquisar itens do campo de edição:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const get_item = (id) => {
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=get_client',
            type: 'post',
            data: {
                id: id,
            },
            beforeSend: function() {
                $('#mod_formul_edit').modal("show");
            },
            success: function retorno_ajax(retorno) {
                var obj = JSON.parse(retorno);
                
                console.log(id);

                $("#frm_id_edit").val(id);

                $("#frm_nome_edit").val(obj[0].cli_name);
                $("#frm_email_edit").val(obj[0].cli_email);
                $("#frm_cpf_edit").val(obj[0].cli_cpf);
                $("#frm_dtNasc_edit").val(obj[0].cli_dtnasc);
                $("#frm_telefone_edit").val(obj[0].cli_telefone);

            }
        });
    }

     /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Excluir pedido:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    function del_item(id) {
        if (confirm("Deseja excluir o cliente?")) {
            if (ajax_div) {
                ajax_div.abort();
            }
            ajax_div = $.ajax({
                cache: false,
                async: true,
                url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=del_client',
                type: 'post',
                data: {
                    id: id,
                },
                success: function retorno_ajax(retorno) {

                    if (retorno == 1) {
                        location.reload();
                        lista_itens();
                    }else {
                        alert("ERRO AO DELETAR ITENS! " + retorno);
                    }
                }
            });
        } else {
            lista_itens();
        }
    }
    
    // Evento inicial:
    $(document).ready(function() {
        lista_itens();
        $('#frm_telefone_edit').mask('(00) 0 0000-0000');
        $('#frm_telefone').mask('(00) 0 0000-0000');
        $('#frm_dtNasc').mask('00/00/0000');
        $('#frm_dtNasc_edit').mask('00/00/0000');
		$('#frm_cpf').mask('000.000.000-00');
		$('#frm_cpf_edit').mask('000.000.000-00');
    });

</script>

<?php
    include("bottom.php");
?>   
