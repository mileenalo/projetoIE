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
            $where .= " AND doc_categoria LIKE '%{$pesquisa}%' OR doc_nivel LIKE '%{$pesquisa}%' OR doc_desc LIKE '%{$pesquisa}%'";
        }

        $usuario = $_COOKIE["idUsuario"];

        $res = $db->select("SELECT * FROM tb_documentos
                            INNER JOIN tb_categorias ON doc_categoria = cat_id
                            INNER JOIN tb_niveis ON doc_nivel = niv_id
                                WHERE doc_usucad = {$usuario}
                                {$where} ORDER BY doc_datacad");

        if (count($res) > 0) {
            echo '<table class="table align-items-center mb-0">';
            echo '  <thead>';
            echo '      <tr>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">Documento</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Data do Cadastro</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Categoria</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Nível</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Abrir</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Editar</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Deletar</th>';
            echo '      </tr>';
            echo '  </thead>';
            echo '  <tbody>';
            foreach($res as $r){
                echo '<tr>';
                echo '  <td class="align-middle text-left">';
                echo '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">'.$r["doc_desc"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.substr($r["doc_datacad"], 6, 2).'-'.substr($r["doc_datacad"], 4, 2).'-'.substr($r["doc_datacad"], 0, 4).'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.$r["cat_desc"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.$r["niv_desc"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold"><a href="'.$r["doc_url"].'" class="open-pdf-modal">Abrir PDF</a></span>';
                echo '  </td>';
                echo '  <td class="align-middle">';
                echo '      <i title="Editar" onclick="get_item(\'' . $r["doc_id"] . '\')" class="fa fa-edit" style="cursor: pointer"></i>';
                echo '  </td>';
                echo '  <td class="align-middle">';
                echo '      <i title="Deletar" onclick="del_item(\'' . $r["doc_id"] . '\')" class="fa fa-trash" style="cursor: pointer"></i>';
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
    if ($_GET["a"] == "inclui_doc") {
       
        if ($_FILES['doc']['error'] === UPLOAD_ERR_OK) {
            $destination_path = getcwd().DIRECTORY_SEPARATOR;
            $nomeArquivo = $_FILES['doc']['name'];
            $caminhoTemporario = $_FILES['doc']['tmp_name'];

            $caminhoDestino = "./arquivos/". $nomeArquivo;

            if(@move_uploaded_file($caminhoTemporario, $caminhoDestino)) {
                echo 'Arquivo enviado e salvo com sucesso.';
            }else{
                echo 'Erro ao salvar o arquivo.';
            }
        }else{
            echo 'Ocorreu um erro durante o envio do arquivo.';
        }
        
        $usuario = $_COOKIE["idUsuario"];
        $desc = $_POST["name"];
        $categ = $_POST["categ"];
        $nivel = $_POST["nivel"];
        $datacad = date("Ymd");

        $res = $db->_exec("INSERT INTO tb_documentos (doc_url, doc_categoria, doc_nivel, doc_desc, doc_usu, doc_datacad) 
                            VALUES ('{$caminhoDestino}', {$categ}, {$nivel}, '{$desc}', {$usuario}, '{$datacad}')");

        echo $res;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Deleta o pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "del_client") {

        $id = $_POST["id"];

        $del = $db->_exec("DELETE FROM tb_documentos WHERE doc_id = {$id}");

        echo $del;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Busca conteúdo para exibir na div de edição do pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "get_doc") {


        $id = $_POST["id"];

        $res = $db->select("SELECT doc_id, doc_categoria, doc_nivel, doc_desc, doc_usu, doc_datacad FROM tb_documentos 
                            WHERE doc_id = {$id}");

        echo json_encode($res);
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Edita informações do documento:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if($_GET["a"] == "edita_doc"){

        $id = $_POST["id"];
        $desc = $_POST["name"];
        $categ = $_POST["categ"];
        $nivel = $_POST["nivel"];
        $datacad = date("Ymd");

        $res = $db->_exec("UPDATE tb_documentos SET doc_categoria = {$categ}, doc_nivel = {$nivel}, 
                                doc_desc = '{$desc}', doc_datacad = '{$datacad}'
                            WHERE doc_id = {$id}");

        echo $res;

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
                    <form id="frm_general" name="frm_general" class="col" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Título:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_nome" placeholder="Ex: Maria Luiza">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_categ" class="form-label">Categoria:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_categ" class="form-control form-control-lg" style="width:100%" name="frm_categ" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT cat_id, cat_desc FROM tb_categorias ORDER BY cat_desc");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["cat_id"].'">'.$s["cat_desc"].'</option>';
                                            }
								        ?>
							        </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_nivel" class="form-label">Nível:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_nivel" class="form-control form-control-lg" style="width:100%" name="frm_nivel" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT niv_id, niv_desc FROM tb_niveis ORDER BY niv_desc");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["niv_id"].'">'.$s["niv_desc"].'</option>';
                                            }
								        ?>
							        </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val2_insert" class="form-label">Upload:</label>
                                <div class="input-group input-group-outline">
                                    <input type="file" class="form-control" id="frm_doc"  placeholder="Ex: material.pdf">
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
                    <form id="frm_general_edit" name="frm_general"  class="col">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Nome:</label>
                                <div class="input-group input-group-outline">
                                    <input id="frm_id_edit" hidden>
                                    <input type="text" class="form-control" id="frm_nome_edit" placeholder="Ex: Maria Luiza">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_categ" class="form-label">Categoria:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_categ_edit" class="form-control form-control-lg" style="width:100%" name="frm_categ_edit" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT cat_id, cat_desc FROM tb_categorias ORDER BY cat_desc");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["cat_id"].'">'.$s["cat_desc"].'</option>';
                                            }
								        ?>
							        </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_nivel_edit" class="form-label">Nível:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_nivel_edit" class="form-control form-control-lg" style="width:100%" name="frm_nivel_edit" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT niv_id, niv_desc FROM tb_niveis ORDER BY niv_desc");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["niv_id"].'">'.$s["niv_desc"].'</option>';
                                            }
								        ?>
							        </select>
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
       
        var doc = $('#frm_doc')[0].files[0];
    
        var formData = new FormData();
        
        formData.append('doc', doc,);
        formData.append('name', $("#frm_nome").val());
        formData.append('categ', $("#frm_categ").val());
        formData.append('nivel', $("#frm_nivel").val());

        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?a=inclui_doc',
            type: 'post',
            data: formData,
            contentType: false, 
            processData: false,
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
    const editUsu = () => {

        if (confirm("Confirma a edição do cliente?")) {
            if (ajax_div) {
                ajax_div.abort();
            }

            ajax_div = $.ajax({
                cache: false,
                async: true,
                url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=edita_doc',
                type: 'post',
                data: {
                    name: $("#frm_nome_edit").val(),
                    categ: $("#frm_categ_edit").val(),
                    nivel: $("#frm_nivel_edit").val(),
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
            url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=get_doc',
            type: 'post',
            data: {
                id: id,
            },
            beforeSend: function() {
                $('#mod_formul_edit').modal("show");
            },
            success: function retorno_ajax(retorno) {
                console.log(retorno);
                var obj = JSON.parse(retorno);
                
                console.log(id);

                $("#frm_id_edit").val(id);

                $("#frm_nome_edit").val(obj[0].doc_desc);
                $("#frm_categ_edit").val(obj[0].doc_categoria);
                $("#frm_nivel_edit").val(obj[0].doc_nivel);
                $("#frm_doc_edit").val("");

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
    });

</script>

<?php
    include("bottom.php");
?>   
