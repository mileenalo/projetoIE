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

    function remove_acento($string){
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
    if ($_GET["a"] == "lista_ped") {

        $pesquisa = $_POST['pesq'];
        $where = "";

        if ($pesquisa != "") {
            $where .= "WHERE pro_descri LIKE '%{$pesquisa}%' OR pro_codbar LIKE '%{$pesquisa}%' OR pro_name LIKE '%{$pesquisa}%' OR cli_name LIKE '%{$pesquisa}%'";
        }

        $res = $db->select("SELECT ped_id, cli_name, usu_name, ped_dataEmiss, ped_valor, ped_qtd, ped_desconto, ped_status FROM tb_pedidos
                            INNER JOIN tb_usuarios ON usu_id = ped_idusu
                            INNER JOIN tb_clientes ON cli_id = ped_idcli
                                {$where} ORDER BY ped_dataEmiss, cli_name DESC");

        if (count($res) > 0) {
            echo '<table class="table align-items-center mb-0">';
            echo '  <thead>';
            echo '      <tr>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">Cliente</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Vendedor</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Valor</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Quantidade</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Data</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Status</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Editar</th>';
            echo '          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Deletar</th>';
            echo '      </tr>';
            echo '  </thead>';
            echo '  <tbody>';
            foreach($res as $r){

                if($r["ped_status"] == '1'){
                    $status = "Iniciado e não finalizado";
                }else{
                    $status = "Finalizado";
                }

                echo '<tr>';
                echo '  <td class="align-middle text-left">';
                echo '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">'.$r["cli_name"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-left">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.$r["usu_name"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-center">';
                echo '    <span class="text-secondary text-xs font-weight-bold">R$'.str_replace(".", ",", $r["ped_valor"]).'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-center">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.$r["ped_qtd"].'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-center">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.substr($r["ped_dataEmiss"], 8, 2)."/".substr($r["ped_dataEmiss"], 5, 2)."/".substr($r["ped_dataEmiss"], 0, 4).'</span>';
                echo '  </td>';
                echo '  <td class="align-middle text-center">';
                echo '    <span class="text-secondary text-xs font-weight-bold">'.$status.'</span>';
                echo '  </td>';
                echo '  <td class="align-middle">';
                echo '      <i title="Editar" onclick="get_item(\'' . $r["ped_id"] . '\')" class="fa fa-edit" style="cursor: pointer"></i>';
                echo '  </td>';
                echo '  <td class="align-middle">';
                echo '      <i title="Deletar" onclick="del_ped(\'' . $r["ped_id"] . '\')" class="fa fa-trash" style="cursor: pointer"></i>';
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
    if ($_GET["a"] == "add_item_ped") {

        $num_ped = $_POST["id"];
        $produto = $_POST["produto"];   
        $cliente = $_POST["cliente"];
        $vendedor = $_POST["vendedor"];
        $quantidade = $_POST["quantidade"];
        $primeiro_item = 2;
        $ret = [];
        $tab = "";

        $prod = $db->select("SELECT pro_id, pro_valvend, pro_quantidade FROM tb_produtos WHERE pro_codbar = '{$produto}' ");
        if(count($prod) > 0){
            foreach($prod as $p){
                $valor = $p["pro_valvend"];
                $idProd = $p["pro_id"];
                $qtdEst = $p["pro_quantidade"];
            }
        }else{
            $ret["inds"] = "Produto cod. ".$produto." não cadastrado!";
            echo json_encode($ret);
            die();
        }
        

        if($qtdEst < $quantidade){
            $ret["inds"] = "INDISPONIBILIDADE EM ESTOQUE!";
            echo json_encode($ret);
            die();
        }

        if($num_ped == ""){
            $res = $db->_exec("INSERT INTO tb_pedidos (ped_idusu, ped_idcli, ped_qtd, ped_status, ped_valor, ped_desconto) VALUES ({$vendedor}, {$cliente}, {$quantidade}, '1', {$valor}, 0) ");
            $res2 = $db->select("SELECT ped_id FROM tb_pedidos ORDER BY ped_id DESC LIMIT 1");
            foreach($res2 as $pd){
                $num_ped = $pd["ped_id"];
            }
            $primeiro_item = 1;
        }

        $res3 = $db->_exec("INSERT INTO tb_itens_pedido (pei_idped, pei_idprod, pei_qtd) VALUES ({$num_ped},{$idProd},{$quantidade})");

        if($primeiro_item <> 1){
            $res4 = $db->_exec("UPDATE tb_pedidos SET ped_valor = ped_valor + {$valor}, ped_qtd = ped_qtd + {$quantidade} WHERE ped_id = {$num_ped}");
        }
        
        $res6 = $db->_exec("UPDATE tb_produtos SET pro_quantidade = pro_quantidade - {$quantidade} WHERE pro_id = {$idProd}");

        $res5 = $db->select("SELECT pro_codbar, pro_name, pei_qtd, pro_valvend, pro_tamanho, pei_id FROM tb_itens_pedido
                            INNER JOIN tb_produtos ON pro_id = pei_idprod
                            WHERE pei_idped = {$num_ped}");
        if(count($res5) > 0) {
            $tab .= '<thead>';
            $tab .= '  <tr>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">Cod. Barras</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Produto</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Tamanho</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Valor</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Quantidade</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Deletar</th>';
            $tab .= '  </tr>';
            $tab .= '</thead>';
            $tab .= '<tbody>';
            $tot = 0;

            foreach($res5 as $r){
                $tot += $r["pro_valvend"] * $r["pei_qtd"];
                $tab .= '<tr>';
                $tab .= '  <td class="align-middle text-left">';
                $tab .= '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">'.$r["pro_codbar"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-left">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.$r["pro_name"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.$r["pro_tamanho"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">R$'.str_replace(".", ",", $r["pro_valvend"]).'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.$r["pei_qtd"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle">';
                $tab .= '      <i title="Deletar" onclick="del_item(\'' . $r["pei_id"] . '\')" class="fa fa-trash" style="cursor: pointer"></i>';
                $tab .= '  </td>';
                $tab .= '</tr>';
            }
            $tab .= '<tr>';
                $tab .= '  <td class="align-middle text-left" colspan="5">';
                $tab .= '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">Total (R$):</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.str_replace(".", ",", $tot).'</span>';
                $tab .= '  </td>';
                $tab .= '</tr>';
            $tab .= '  </tbody>';
            $tab .= '</table>';
        } else{
            $tab .= '<div class="alert alert-warning" role="alert" style="margin-left: 15px;margin-right:25px;">';
            $tab .= 'Nenhum registro localizado!';
            $tab .= '</div>';
        }

        $ret["num_ped"] = $num_ped;
        $ret["tab_itens"] = $tab;

        echo json_encode($ret);
    }

    if($_GET["a"] == "inclui_ped"){
        
        $id = $_POST["id"];

        if($id == ""){
            $id = $_POST["idedit"];
        }

        $desc = $_POST["decPer"];
        $descnu = $_POST["desconto"];
        $date = date("Ymd");
        
        if($descnu == "" && $desc <> ""){
            $busc = $db->select("SELECT ped_valor FROM tb_pedidos WHERE ped_id = {$id} ");
            foreach($busc as $b){
                $valor = $b["ped_valor"];
            }

            $desconto = ($desc/100) * $valor;
        }elseif($descnu <> "" && $desc == ""){
            $desconto = $descnu;
        }else{
            $desconto = 0;
        }
      
        $ex = $db->_exec("UPDATE tb_pedidos SET ped_status = '2', ped_desconto = {$desconto}, ped_dataEmiss = '{$date}' WHERE ped_id = {$id}");

        echo $ex;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * editar conteúdo dentro da lista de pedidos do modal de edição:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "edita_product") {

        $id = $_POST["id"];
        $name = $_POST["name"];
        $descri = $_POST["descri"];
        $valor = str_replace(",", ".", $_POST["valor"]);
        $quantidade = $_POST["quantidade"];
        $tamanho = $_POST["tamanho"];
        $codbar = $_POST["codbar"];
        
        $res = $db->_exec("UPDATE tb_product
                            SET pro_name = '{$name}', pro_descri = '{$descri}', 
                            pro_tamanho = '{$tamanho}', pro_valvend = {$valor}, 
                            pro_codbar = '{$codbar}', pro_quantidade = {$quantidade}
                        WHERE pro_id = $id");

        echo $res;
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Deleta o pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "del_product") {

        $id = $_POST["id"];

        $sel = $db->select("SELECT pei_qtd, pro_valvend, pei_idped, pro_id FROM tb_itens_pedido
                            INNER JOIN tb_produtos ON pei_idprod = pro_id
                            WHERE pei_id = {$id}");
        foreach($sel as $s){
            $valor = $s["pro_valvend"];
            $qtd = $s["pei_qtd"];
            $num_ped = $s["pei_idped"];
            $pro_id = $s["pro_id"];
        }
        $del = $db->_exec("DELETE FROM tb_itens_pedido WHERE pei_id = {$id}");

        $ex = $db->_exec("UPDATE tb_pedidos SET ped_qtd = ped_qtd - {$qtd}, ped_valor = ped_valor - {$valor} WHERE ped_id = {$num_ped}");
        $ex2 = $db->_exec("UPDATE tb_produtos SET pro_quantidade = pro_quantidade + {$qtd} WHERE pro_id = {$pro_id}");

        echo $del;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Deleta o pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "del_pedido") {

        $id = $_POST["id"];
       
        $sel = $db->select("SELECT pei_id, pei_qtd, pei_idprod FROM tb_itens_pedido WHERE pei_idped = {$id}");
        
        foreach($sel as $s){
            $idItem = $s["pei_id"];
            $qtdItem = $s["pei_qtd"];
            $idProd = $s["pei_idprod"];
            $del = $db->_exec("DELETE FROM tb_itens_pedido WHERE pei_id = {$idItem}");
            $ex2 = $db->_exec("UPDATE tb_produtos SET pro_quantidade = pro_quantidade + {$qtdItem} WHERE pro_id = {$idProd}");
        }

        $ex = $db->_exec("DELETE FROM tb_pedidos WHERE ped_id = {$id}");

        echo $ex;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Busca conteúdo para exibir na div de edição do pedido:
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    if ($_GET["a"] == "get_pedido") {

        $num_ped = $_POST["id"];
        $cli = "";
        $usu = "";
        $ret = [];

        $res5 = $db->select("SELECT pro_codbar, pro_name, pei_qtd, pro_valvend, pro_tamanho, ped_idcli, ped_idusu, pei_id FROM tb_itens_pedido
                            INNER JOIN tb_produtos ON pro_id = pei_idprod
                            INNER JOIN tb_pedidos ON pei_idped = ped_id
                            WHERE pei_idped = {$num_ped}");
        if(count($res5) > 0) {
            $tab .= '<thead>';
            $tab .= '  <tr>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-left">Cod. Barras</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Produto</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Tamanho</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Valor</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Quantidade</th>';
            $tab .= '      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-left">Deletar</th>';
            $tab .= '  </tr>';
            $tab .= '</thead>';
            $tab .= '<tbody>';
            $tot = 0;

            foreach($res5 as $r){
                $usu = $r["ped_idusu"];
                $cli = $r["ped_idcli"];
                $tot += $r["pro_valvend"] * $r["pei_qtd"];
                $tab .= '<tr>';
                $tab .= '  <td class="align-middle text-left">';
                $tab .= '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">'.$r["pro_codbar"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-left">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.$r["pro_name"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.$r["pro_tamanho"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">R$'.str_replace(".", ",", $r["pro_valvend"]).'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.$r["pei_qtd"].'</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle">';
                $tab .= '      <i title="Deletar" onclick="del_item(\'' . $r["pei_id"] . '\')" class="fa fa-trash" style="cursor: pointer"></i>';
                $tab .= '  </td>';
                $tab .= '</tr>';
            }
            $tab .= '<tr>';
                $tab .= '  <td class="align-middle text-left" colspan="5">';
                $tab .= '      <span class="text-secondary text-xs font-weight-bold" style="padding-left:15px;">Total (R$):</span>';
                $tab .= '  </td>';
                $tab .= '  <td class="align-middle text-center">';
                $tab .= '    <span class="text-secondary text-xs font-weight-bold">'.str_replace(".", ",", $tot).'</span>';
                $tab .= '  </td>';
                $tab .= '</tr>';
            $tab .= '  </tbody>';
            $tab .= '</table>';
        } else{
            $tab .= '<div class="alert alert-warning" role="alert" style="margin-left: 15px;margin-right:25px;">';
            $tab .= 'Nenhum registro localizado!';
            $tab .= '</div>';
        }

        $ret["id_usu"] = $usu;
        $ret["id_cli"] = $cli;
        $ret["tab_itens"] = $tab;

        echo json_encode($ret);
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
                            <h6 class="text-white text-capitalize ps-3">Pedidos</h6>
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
                            <h5 id="tit_frm_formul" class="modal-title">Incluir Pedido</h5>
                        </div>
                    </div>
                    <button type="button" style="cursor: pointer; border: 1px solid #ccc; border-radius: 10px" aria-label="Fechar" onclick="$('#mod_formul').modal('hide');">X</button>
                </div>
                <div class="modal-body modal-dialog-scrollable">
                    <form id="frm_general" name="frm_general" class="col">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="frm_val1_insert" class="form-label">Vendedor:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_vend" class="form-control form-control-lg" style="width:100%" name="frm_vend" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT usu_id, usu_name FROM tb_usuarios ORDER BY usu_name");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["usu_id"].'">'.$s["usu_name"].'</option>';
                                            }
								        ?>
							        </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="frm_val1_insert" class="form-label">Cliente:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_cliente" class="form-control form-control-lg" style="width:100%" name="frm_cliente" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT cli_id, cli_name FROM tb_clientes ORDER BY cli_name");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["cli_id"].'">'.$s["cli_name"].'</option>';
                                            }
								        ?>
							        </select>
                                    <input id="frm_num_ped" type="number" hidden> 
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-10">
                                <label for="frm_val1_insert" class="form-label">Produtos:</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_produto" placeholder="" onkeypress="addItemPed();">
                                </div>
                            </div>
                            <div class="col-4" style="padding-right: 5px;">
                                <div class="input-group input-group-outline">
                                    <input id="frm_qtd" type="number" value="1" hidden> 
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                               <table id="tab_produtos_add"></table>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Desconto (R$):</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_desconto" placeholder="">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Desconto (%):</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_desconto_perc" placeholder="">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#mod_formul').modal('hide');">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="OK" onclick="incluiPro();"><img id="img_btn_ok" style="width: 15px; display: none; margin-right: 10px">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal formulário Inclusao -->
    <div class="modal" id="mod_formul_edit">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" style="max-width: 50%;">
            <div class="modal-content">
                <div class="modal-header" style="align-items: center">
                    <div style="display: flex; align-items: center">
                        <div style="margin-right: 5px">
                            <h2 style="margin: 0"><span class="badge bg-info text-white" style="padding: 8px" id="span_endereco_nome"></span></h2>
                        </div>
                        <div>
                            <h5 id="tit_frm_formul" class="modal-title">Editar Pedido</h5>
                        </div>
                    </div>
                    <button type="button" style="cursor: pointer; border: 1px solid #ccc; border-radius: 10px" aria-label="Fechar" onclick="$('#mod_formul_edit').modal('hide');">X</button>
                </div>
                <div class="modal-body modal-dialog-scrollable">
                    <form id="frm_general" name="frm_general" class="col">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="frm_val1_insert" class="form-label">Vendedor:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_vend_edit" class="form-control form-control-lg" style="width:100%" name="frm_vend_edit" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT usu_id, usu_name FROM tb_usuarios ORDER BY usu_name");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["usu_id"].'">'.$s["usu_name"].'</option>';
                                            }
								        ?>
							        </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="frm_val1_insert" class="form-label">Cliente:</label>
                                <div class="input-group input-group-outline">
                                    <select id="frm_cliente_edit" class="form-control form-control-lg" style="width:100%" name="frm_cliente_edit" type="text">
								        <option value="" selected></option>
								        <?php
                                            $desc = $db->select("SELECT cli_id, cli_name FROM tb_clientes ORDER BY cli_name");
                                            foreach($desc as $s){
                                                echo  '<option value="'.$s["cli_id"].'">'.$s["cli_name"].'</option>';
                                            }
								        ?>
							        </select>
                                    <input id="frm_num_ped_edit" type="number" hidden> 
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="row mb-4">
                                <div class="col-10">
                                    <label for="frm_val1_insert" class="form-label">Produtos:</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" class="form-control" id="frm_produto_edit" placeholder="" onblur="addItemPed();">
                                    </div>
                                </div>
                                <div class="col-4" style="padding-right: 5px;">
                                    <div class="input-group input-group-outline">
                                        <input id="frm_qtd_edit" type="number" value="1" hidden> 
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                <table id="tab_produtos_add"></table>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                               <table id="tab_produtos_add_edit"></table>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Desconto (R$):</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_desconto_edit" placeholder="">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="frm_val1_insert" class="form-label">Desconto (%):</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" id="frm_desc_perc_edit" placeholder="">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#mod_formul_edit').modal('hide');">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="OK" onclick="incluiPro();"><img id="img_btn_ok" style="width: 15px; display: none; margin-right: 10px">OK</button>
                </div>
            </div>
        </div>
    </div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
   
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Listar itens:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const lista_itens = () => {
        qty_oper = 0;
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?a=lista_ped',
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
     * Listar itens:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const sel_desc = (produto) => {
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?a=get_desc',
            type: 'post',
            data: {
                produto: produto,
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
    const incluiPro = () => {
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?a=inclui_ped',
            type: 'post',
            data: {
                id: $("#frm_num_ped").val(),
                idedit: $("#frm_num_ped_edit").val(),
                desconto: $("#frm_desconto").val(),
                decPer: $("#frm_desconto_perc").val(),
            },
            success: function retorno_ajax(retorno) {
                if (!retorno) {
                    alert("ERRO AO INLUIR PEDIDO!");
                }else{
                    $("#mod_formul").modal('hide');
                    $("#mod_formul_edit").modal('hide');
                    lista_itens();
                }
            }
        });
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * permite a edição de itens dentro do pedido:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    const editPro = (countarray, iditens, idPed) => {

        if (confirm("Confirma a edição do produto?")) {
            if (ajax_div) {
                ajax_div.abort();
            }

            ajax_div = $.ajax({
                cache: false,
                async: true,
                url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=edita_client',
                type: 'post',
                data: {
                    name: $("#frm_nome").val(),
                    descri: $("#frm_descri").val(),
                    valor: $("#frm_valor").val(),
                    codbar: $("#frm_codbar").val(),
                    tamanho: $("#frm_tamanho").val(),
                    quantidade: $("#frm_quantidade").val(),
                    id: $('#frm_id_edit').val(),
                },
                beforeSend: function() {
                    $('#mod_formul_edit').modal("show");
                },
                success: function retorno_ajax(retorno) {
                    if (!retorno) {
                        alert("ERRO AO EDITAR O PRODUTO!");
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
    const addItemPed = () => {
        if (ajax_div) {
            ajax_div.abort();
        }
        ajax_div = $.ajax({
            cache: false,
            async: true,
            url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=add_item_ped',
            type: 'post',
            data: {
                id: $("#frm_num_ped").val(),
                produto: $("#frm_produto").val(),
                cliente: $("#frm_cliente").val(),
                vendedor: $("#frm_vend").val(),
                quantidade: $("#frm_qtd").val(),
            },
            success: function retorno_ajax(retorno) {
                console.log(retorno);

                var obj = JSON.parse(retorno);
                if(obj.inds != null){
                    alert(obj.inds);
                }
                $("#frm_num_ped").val(obj.num_ped);

                $("#tab_produtos_add").html(obj.tab_itens);
                $("#frm_produto").val('');
            }
        });
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
            url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=get_pedido',
            type: 'post',
            data: {
                id: id,
            },
            beforeSend: function() {
                $('#mod_formul_edit').modal("show");
            },
            success: function retorno_ajax(retorno) {
                var obj = JSON.parse(retorno);
                
                console.log(retorno);

                $("#frm_num_ped_edit").val(id);

                $("#frm_cliente_edit").val(obj.id_cli);
                $("#frm_vend_edit").val(obj.id_usu);
                $("#tab_produtos_add_edit").html(obj.tab_itens);

            }
        });
    }

     /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Excluir pedido:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    function del_item(id) {
        if (confirm("Deseja excluir o produto do pedido?")) {
            if (ajax_div) {
                ajax_div.abort();
            }
            ajax_div = $.ajax({
                cache: false,
                async: true,
                url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=del_product',
                type: 'post',
                data: {
                    id: id,
                },
                success: function retorno_ajax(retorno) {

                    if (retorno == 1) {
                        location.reload();
                        get_item($("#frm_num_ped_edit").val());
                        get_item($("#frm_num_ped").val());
                    }else {
                        alert("ERRO AO DELETAR ITEM DO PEDIDO! " + retorno);
                    }
                }
            });
        } else {
            lista_itens();
        }
    }
    
     /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Excluir pedido:
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    var ajax_div = $.ajax(null);
    function del_ped(id) {
        if (confirm("Deseja excluir o pedido?")) {
            if (ajax_div) {
                ajax_div.abort();
            }
            ajax_div = $.ajax({
                cache: false,
                async: true,
                url: '?uid=<?php echo $_COOKIE['idUsuario']; ?>&a=del_pedido',
                type: 'post',
                data: {
                    id: id,
                },
                success: function retorno_ajax(retorno) {
                    if (retorno == 1) {
                        location.reload();
                        lista_itens();
                    }else {
                        alert("ERRO AO DELETAR PEDIDO! " + retorno);
                    }
                }
            });
        } else {
            lista_itens();
        }
    }

    // Evento inicial:
    $(document).ready(function() {
        
        $("#frm_vend").select2({
  			dropdownParent: $('#mod_formul')
		}).on("select2:open", function () {
  			$(".select2-container--open").css("z-index", "1400"); // substitua o valor de acordo com as necessidades
		});

		$("#frm_cliente").select2({
			dropdownParent: $('#mod_formul')
		}).on("select2:open", function () {
			$(".select2-container--open").css("z-index", "1400"); // substitua o valor de acordo com as necessidades
		});

        lista_itens();
    });

</script>

<?php
    include("bottom.php");
?>   
