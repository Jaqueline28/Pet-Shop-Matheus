<?php
function loged() {
    // Verifica se o usuário está logado
    if (isset($_SESSION['tipo'])) {
        return isset($_SESSION['loggedinFun']) && $_SESSION['loggedinFun'];
    } else {
        return isset($_SESSION['loggedinCli']) && $_SESSION['loggedinCli'];
    }
}

function logoff() {
    // Desloga o usuário
    if (isset($_SESSION['tipo'])) {
        unset($_SESSION['loggedinFun']);
        unset($_SESSION['idFun']);
        unset($_SESSION['nome']);
        unset($_SESSION['tipo']);
    } else {
        unset($_SESSION['loggedinCli']);
        unset($_SESSION['idCli']);
    }
}

function gerarTabelaAni() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    $header = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/Pet-Shop/pages/clientes/animaisCli.php';

    // Receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

    // Setar a quantidade de items por pagina
    $qnt_result_pg = 5;

    // Calcular o inicio visualização
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    // String de preparação
    $stmt = $conn->prepare("SELECT nome, data_nascimento, raca, peso, pk_Animal
        FROM Animais WHERE fk_Cliente = ? AND ativo = 'ativo' ORDER BY nome LIMIT $inicio, $qnt_result_pg");
    // Substituição da string preparada pelos valores corretos
    $stmt->bind_param("s", $_SESSION['idCli']);
    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela = "<thead><tr>
            <th>Nome</th>
            <th>Data de Nascimento</th>
            <th>Raça</th>
            <th>Peso</th>
            <th>Alterar</th>
            <th>Excluir</th>
        </tr></thead>";

    if (mysqli_num_rows($resultado) == 0) {
        $tabela = $tabela . "
            <tr>
                <td colspan=6>Não há animais cadastrados</td>
            </tr>
            ";
    } else {
        // Pega cada linha da query e monta as linhas da tabela
        foreach ($resultado->fetch_all() as $row) {
            // Formata a data
            $data = date('d/m/Y', strtotime($row[1]));
            $tabela = $tabela .
                "<tr>
                    <td>$row[0]</td>
                    <td>$data</td>
                    <td>$row[2]</td>
                    <td>$row[3] Kg</td>
                    <td><a href='http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/Pet-Shop/pages/cliente/altAnimal.php?id="
                . $row[4] . "'><i class='bi bi-pencil-square'></i></a></td>
                    <td><a href='http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/Pet-Shop/backend/processos/proc_excAnimal.php?id="
                . $row[4] . "'><i class='bi bi-trash'></i></a></td>
                </tr>";
        }
    }

    // Paginação - Somar a quantidade de usuários
    $result_pg = "SELECT COUNT(PK_Animal) AS num_result FROM Animais";
    $resultado_pg = mysqli_query($conn, $result_pg);
    $row_pg = mysqli_fetch_assoc($resultado_pg);

    // Quantidade de pagina
    $quantidade_pg = ceil($row_pg['num_result'] / $qnt_result_pg);

    // Limitar os link antes depois
    $max_links = 2;
    $linkPaginas = "<a href='$header?pagina=1'><<</a> ";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_ant'>$pag_ant</a> ";
        }
    }

    $linkPaginas =  $linkPaginas . $pagina;

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $quantidade_pg) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_dep'>$pag_dep</a> ";
        }
    }

    $linkPaginas = $linkPaginas . " <a href='$header?pagina=$quantidade_pg'>>></a>";

    $retornar = array('animais', $tabela, 'links', $linkPaginas);
    return json_encode($retornar);
}

function altAnimal() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    // String de preparação
    $stmt = $conn->prepare("SELECT nome, data_nascimento, especie, raca, peso, cor FROM Animais WHERE pk_Animal = ?");
    // Substituição da string preparada pelos valores corretos
    $stmt->bind_param("s", $_GET['idAni']);
    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all()[0];

    header('Content-Type: application/json');
    return json_encode($data);
}

function gerarTabelaAgenCli() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    $header = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/Pet-Shop/pages/clientes/agendamentosCli.php';

    // Receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

    // Setar a quantidade de items por pagina
    $qnt_result_pg = 5;

    // Calcular o inicio visualização
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    // String de preparação
    $stmt = $conn->prepare("SELECT Funcionarios.nome, data_agendamento,
        horario_agendamento, Animais.nome, Agendamentos.tipo, `status`, pk_Agendamento FROM Agendamentos
            INNER JOIN Animais
            ON Agendamentos.fk_Animal = Animais.pk_Animal
            INNER JOIN Clientes
            ON Animais.fk_Cliente = Clientes.pk_Cliente
            INNER JOIN Funcionarios
            ON Agendamentos.fk_Funcionario = Funcionarios.pk_Funcionario
            WHERE pk_Cliente = ?
            ORDER BY data_agendamento, horario_agendamento");
    // Substituição da string preparada pelos valores corretos
    $stmt->bind_param("s", $_SESSION['idCli']);
    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela = "<thead><tr>
            <th>Profissional</th>
            <th>Data Agendamento</th>
            <th>Horário do agendamento</th>
            <th>Nome do animal</th>
            <th>Tipo</th>
            <th>Detalhes</th>
            <th>Status</th>
        </tr></thead>";

    if (mysqli_num_rows($resultado) == 0) {
        $tabela = $tabela . "
            <tr>
                <td colspan=7>Não há agendamentos cadastrados</td>
            </tr>
            ";
    } else {
        // Pega cada linha da query e monta as linhas da tabela
        foreach ($resultado->fetch_all() as $row) {
            // Formata a data
            $data = date('d/m/Y', strtotime($row[1]));
            $botao = "";

            if ($row[5] == "Marcado") {
                $botao = "<button class='cancelar' onclick='activeModal($row[6]," . '"Cancelar"' . ")'>Cancelar</button>";
            } elseif ($row[5] == "Concluido") {
                $botao = "<button class='finalizar cancelar' onclick='activeModal($row[6]," . '"Detalhes"' . ")'>Detalhes</button>";
            }

            $tabela = $tabela .
                "<tr>
                    <td>$row[0]</td>
                    <td>$data</td>
                    <td>$row[2]</td>
                    <td>$row[3]</td>
                    <td>$row[4]</td>
                    <td>$botao</td>
                    <td>$row[5]</td>
                </tr>";
        }
    }

    // Paginação - Somar a quantidade de usuários
    $result_pg = "SELECT COUNT(PK_Agendamento) AS num_result FROM Agendamentos";
    $resultado_pg = mysqli_query($conn, $result_pg);
    $row_pg = mysqli_fetch_assoc($resultado_pg);

    // Quantidade de pagina
    $quantidade_pg = ceil($row_pg['num_result'] / $qnt_result_pg);

    // Limitar os link antes depois
    $max_links = 2;
    $linkPaginas = "<a href='$header?pagina=1'><<</a> ";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_ant'>$pag_ant</a> ";
        }
    }

    $linkPaginas =  $linkPaginas . $pagina;

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $quantidade_pg) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_dep'>$pag_dep</a> ";
        }
    }

    $linkPaginas = $linkPaginas . " <a href='$header?pagina=$quantidade_pg'>>></a>";

    $retornar = array("agendamentos", $tabela, 'links', $linkPaginas);
    return json_encode($retornar);
}

function checkAnimais() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $stmt = $conn->prepare("SELECT pk_Animal, nome FROM Animais WHERE fk_Cliente = ? AND ativo = 'ativo' ORDER BY nome");
    $stmt->bind_param("s", $_SESSION['idCli']);
    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    $opcoes = "<option value='0' disabled selected hidden>Selecione um animal</option>";

    if (mysqli_num_rows($resultado) == 0) {
        $opcoes = $opcoes . "
            <option selected disabled>Cadastre um Animal</option>
            ";
    } else {
        foreach ($resultado->fetch_all() as $row) {
            $opcoes = $opcoes . "<option value='$row[0]'>$row[1]</option>";
        }
    }

    $retornar = array("animais", $opcoes);
    return json_encode($retornar);
}

function gerarTabelaFazAgenCli() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    // String de preparação
    $stmt = $conn->prepare("SELECT Funcionarios.nome, data_agendamento,
        horario_agendamento, pk_Agendamento FROM Agendamentos
            INNER JOIN Funcionarios
            ON Agendamentos.fk_Funcionario = Funcionarios.pk_Funcionario
            WHERE `status` = 'Disponivel' AND data_agendamento LIKE ? AND tipo = ?
            ORDER BY data_agendamento, horario_agendamento");

    // Substituição da string preparada pelos valores corretos
    $data_atual = date("Y-m-d");
    $hora_atual = date("H:i");

    $data = "%" . $_GET['data'] . "%";
    $stmt->bind_param("ss", $data, $_GET['tipo']);
    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela = "<thead><tr>
            <th>Profissional</th>
            <th>Data Agendamento</th>
            <th>Horário do agendamento</th>
            <th>Agendar</th>
        </tr></thead>";

    if (mysqli_num_rows($resultado) == 0) {
        $tabela = $tabela . "
            <tr>
                <td colspan=7>Não há agendamentos cadastrados</td>
            </tr>
            ";
    } else {
        // Pega cada linha da query e monta as linhas da tabela
        foreach ($resultado->fetch_all() as $row) {

            if (strtotime($data_atual) > strtotime($row[1]) || (strtotime($data_atual) == strtotime($row[1]) && strtotime($hora_atual) > strtotime($row[2]))){
                $stmt2 = $conn -> prepare("UPDATE Agendamentos set `status` = 'Cancelado'
                where pk_Agendamento = ?");

                $stmt2 -> bind_param("s", $row[3]);

                $stmt2-> execute();
            } else {
                // Formata a data
                $data = date('d/m/Y', strtotime($row[1]));
                $tabela = $tabela .
                    "<tr>
                        <td>$row[0]</td>
                        <td>$data</td>
                        <td>$row[2]</td>
                        <td><button class='cancelar agendar' type='button' onclick='executeFunctions(" . '"fazAgendamentoCli",' . $row[3] . ")'>Agendar</button></td>
                    </tr>";
            }
        }
    }

    $retornar = array("fazAgend", $tabela);
    return json_encode($retornar);
}

function fazAgendamentoCli() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    if ($_GET['idAni'] != 0) {
        try {
            $stmt = $conn->prepare("UPDATE Agendamentos SET fk_Animal = ?, `status` = 'Marcado' WHERE pk_Agendamento = ?");
            $stmt->bind_param("ss", $_GET['idAni'], $_GET['idAgen']);
            // Executa o sql
            $stmt->execute();

            $_SESSION['msgAgendamentoCli'] = "Agendamento Realizado";
            return "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/Pet-Shop/pages/cliente/agendamentosCli.php";
        } catch (Exception $e) {
            $_SESSION['msgFazAgendamento'] = "Error: " . $e->getMessage();
            return "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/Pet-Shop/pages/cliente/fazerAgendamentoCli.php";
        }
    } else {
        $_SESSION['msgFazAgendamento'] = "Selecione um animal!";
        return "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/Pet-Shop/pages/cliente/fazerAgendamentoCli.php";
    }
}

function gerarTabelaAgenFun() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    $header = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/Pet-Shop/pages/funcionario/agendamentosFun.php';

    // Receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

    // Setar a quantidade de items por pagina
    $qnt_result_pg = 5;

    // Calcular o inicio visualização
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    $status = $_GET['status'];

    if ($status == '') {
        $status = 'Disponivel';
    }

    if ($_SESSION['tipo'] == 'Veterinario' || $_SESSION['tipo'] == 'Esteticista') {

        // String de preparação
        $stmt = $conn->prepare("SELECT Funcionarios.nome, data_agendamento, horario_agendamento, Animais.nome, Clientes.nome, `status`, pk_Agendamento FROM Agendamentos
            LEFT JOIN Animais
            ON Agendamentos.fk_Animal = Animais.pk_Animal
            LEFT JOIN Clientes
            ON Animais.fk_Cliente = Clientes.pk_Cliente
            INNER JOIN Funcionarios
            ON Agendamentos.fk_Funcionario = Funcionarios.pk_Funcionario
            WHERE fk_Funcionario = ?
            AND Funcionarios.nome LIKE ?
            AND `status` = ?
            ORDER BY data_agendamento, horario_agendamento");

        $pesquisar = "%" . $_GET['pesq'] . "%";
        // Substituição da string preparada pelos valores corretos
        $stmt->bind_param("sss", $_SESSION['idFun'], $pesquisar, $status);

    } else {
        $stmt = $conn->prepare("SELECT Funcionarios.nome, data_agendamento, horario_agendamento, Animais.nome, Clientes.nome, `status`, pk_Agendamento from Agendamentos
            LEFT JOIN Animais
            ON Agendamentos.fk_Animal = Animais.pk_Animal
            LEFT JOIN Clientes
            ON Animais.fk_Cliente = Clientes.pk_Cliente
            INNER JOIN Funcionarios
            ON Agendamentos.fk_Funcionario = Funcionarios.pk_Funcionario
            WHERE Funcionarios.nome LIKE ?
            AND `status` = ?
            ORDER BY data_agendamento, horario_agendamento");

        $pesquisar = "%" . $_GET['pesq'] . "%";
        $stmt->bind_param("ss", $pesquisar, $status);
    }

    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela =
        "<thead><tr>
            <th>Profissional</th>
            <th>Data</th>
            <th>Horário</th>
            <th>Animal</th>
            <th>Dono</th>
            <th>Detalhes</th>
            <th>Status</th>
        </tr></thead>";

    if (mysqli_num_rows($resultado) == 0){
        $tabela = $tabela . "
            <tr>
                <td colspan=7>Não há agendamentos cadastrados</td>
            </tr>
            ";
    } else {
        foreach ($resultado->fetch_all() as $row) {
            // Formata a data
            $det = "<button class='cancelar finalizar' onclick='executeFunctions(" . '"update"' . ", $row[6])'>Finalizar</button>";
    
            if ($row[3] == '') {
                $det = '';
    
            } elseif ($row[5] == "Concluido" && $row[3] != '') {
    
                $det = "<button class='finalizar cancelar' onclick='activeModalDetalhesFun($row[6]," . '"' . $_SESSION['tipo'] . '"' . ")'>Detalhes</button>";
            }
    
            $data = date('d/m/Y', strtotime($row[1]));
            $tabela = $tabela .
                "<tr>
                    <td>$row[0]</td>
                    <td>$data</td>
                    <td>$row[2]</td>
                    <td>$row[3]</td>
                    <td>$row[4]</td>
                    <td>$det</td>
                    <td>$row[5]</td>
                </tr>";
        }
    }

    // Pega cada linha da query e monta as linhas da tabela

    // Paginação - Somar a quantidade de usuários
    $result_pg = "SELECT COUNT(PK_Agendamento) AS num_result FROM Agendamentos";
    $resultado_pg = mysqli_query($conn, $result_pg);
    $row_pg = mysqli_fetch_assoc($resultado_pg);

    // Quantidade de pagina
    $quantidade_pg = ceil($row_pg['num_result'] / $qnt_result_pg);

    // Limitar os link antes depois
    $max_links = 2;
    $linkPaginas = "<a href='$header?pagina=1'><<</a> ";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_ant'>$pag_ant</a> ";
        }
    }

    $linkPaginas =  $linkPaginas . $pagina;

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $quantidade_pg) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_dep'>$pag_dep</a> ";
        }
    }

    $linkPaginas = $linkPaginas . " <a href='$header?pagina=$quantidade_pg'>>></a>";

    $retornar = array('tabela', $tabela, 'links', $linkPaginas);
    return json_encode($retornar);
}

function profissionais() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $prof = $_GET['servico'];

    if ($prof == 'Banho' || $prof == 'Tosa' || $prof == "Banho e Tosa"){
        $prof = "Esteticista";
    }

    $stmt = $conn->prepare("SELECT nome, pk_Funcionario FROM Funcionarios WHERE profissao = ? and ativo = 'ativo' ORDER BY nome");

    $stmt->bind_param("s", $prof);

    // Executa o sql
    $stmt->execute();

    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    $tabela = "<option value='' disabled selected hidden>Selecione um funcionário</option>";

    foreach ($resultado->fetch_all() as $row) {
        $tabela = $tabela . "<option value='$row[0]'>$row[0]</option>";
    }

    $retornar = array('profissionais', $tabela);
    return json_encode($retornar);
}

function getDesc() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT descricao FROM Agendamentos WHERE pk_Agendamento = ?");
    $stmt->bind_param("s", $id);
    // Executa o sql
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row = $resultado->fetch_all()[0][0];

    if ($row == "") {
        $retornar = "Os detalhes não foram adicionados ainda";
    } else {
        $retornar = $row;
    }

    return $retornar;
}


function gerarTabelaDeleteFun() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    $header = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/Pet-Shop/pages/Jfuncionario/listarFuncionario.php';

    // Receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

    // Setar a quantidade de items por pagina
    $qnt_result_pg = 5;

    // Calcular o inicio visualização
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    // String de preparação
    $stmt = $conn->prepare("SELECT nome, cpf, profissao, pk_Funcionario, ativo FROM Funcionarios
        WHERE nome LIKE ?
        AND profissao != 'admin'
        AND ativo = ?
        ORDER BY nome ASC");

    $pesquisar = "%" . $_GET['pesq'] . "%";

    if ($_GET['situ'] == '') {
        $situacao = 'ativo';
    } else {
        $situacao = $_GET['situ'];
    }


    // Substituição da string preparada pelos valores corretos
    $stmt->bind_param("ss", $pesquisar, $situacao);

    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela =
        "<thead><tr>
            <th>Nome</th>
            <th>Cpf</th>
            <th>Profissão</th>
            <th>Demitir</th>
        </tr></thead>";

    $cont = 1;
    if (mysqli_num_rows($resultado) == 0){
        $tabela = $tabela . "
            <tr>
                <td colspan=7>Não há agendamentos cadastrados</td>
            </tr>
            ";
    } else {
        // Pega cada linha da query e monta as linhas da tabela
        foreach ($resultado->fetch_all() as $row) {
            $button = "<button class='cancelar' onclick='executeFunctions(`apagarFun`," . $row[3] . ")'>Demitir";

            if ($row[4] == 'demitido') {
                $button = '<p>demitido</p>';
            }
            $tabela = $tabela .
                "<tr>
                    <td id='nome$cont'>$row[0]</td>
                    <td>$row[1]</td>
                    <td>$row[2]</td>
                    <td>$button</td>
                </tr>";
            $cont += 1;
        }
    }
    
    // Paginação - Somar a quantidade de usuários
    $result_pg = "SELECT COUNT(PK_Funcionario) AS num_result FROM Funcionarios";
    $resultado_pg = mysqli_query($conn, $result_pg);
    $row_pg = mysqli_fetch_assoc($resultado_pg);

    // Quantidade de pagina
    $quantidade_pg = ceil($row_pg['num_result'] / $qnt_result_pg);

    // Limitar os link antes depois
    $max_links = 2;
    $linkPaginas = "<a href='$header?pagina=1'><<</a> ";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_ant'>$pag_ant</a> ";
        }
    }

    $linkPaginas =  $linkPaginas . $pagina;

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $quantidade_pg) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_dep'>$pag_dep</a> ";
        }
    }

    $linkPaginas = $linkPaginas . " <a href='$header?pagina=$quantidade_pg'>>></a>";

    $retornar = array('tabela', $tabela, 'links', $linkPaginas);
    return json_encode($retornar);
}

function update($table, $set, $where, $param) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $stmt = $conn->prepare("UPDATE $table
        SET $set
        WHERE $where");
    $stmt->bind_param($param[0], $param[1]);

    $stmt->execute();
}

function apagarFuncionario() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $id = $_GET['id'];

    $stmt = $conn->prepare("UPDATE Funcionarios
        SET ativo = 'demitido'
        WHERE pk_Funcionario = ?");
    $stmt->bind_param("s", $id);

    $stmt->execute();


}

function finalizarConsul() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $id = $_GET['id'];
    $a = "Concluido";

    $stmt = $conn->prepare("UPDATE Agendamentos
        SET `status` = ?
        WHERE pk_Agendamento = ?");

    $stmt->bind_param("ss", $a, $id);

    $stmt->execute();

}

function altMeuPerfilCli() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    // String de preparação
    $stmt = $conn->prepare("SELECT cpf, nome, sobrenome, celular, cep, logradouro,
        numero, complemento, bairro, municipio, uf, email FROM Clientes WHERE pk_Cliente = ?");
    // Substituição da string preparada pelos valores corretos
    $stmt->bind_param("s", $_GET['idCli']);
    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all()[0];

    header('Content-Type: application/json');
    return json_encode($data);
}


function animais() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $prof = $_GET['cpf'];

    $stmt = $conn->prepare("SELECT pk_Cliente from Clientes
        where cpf = ?");

    $stmt->bind_param("s", $prof);

    // Executa o sql
    $stmt->execute();

    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    if (mysqli_num_rows($resultado) == 0){
        $tabela = "<option value='' disabled selected hidden>CPF não está no sistema</option>";
    } else {

        $idCliente = $resultado -> fetch_all()[0][0];

        $stmt2 = $conn -> prepare("SELECT nome, pk_Animal from Animais where fk_Cliente = ?");

        $stmt2 -> bind_param("s", $idCliente);

        $stmt2 -> execute();

        $resultado2 = $stmt2->get_result();

        if (mysqli_num_rows($resultado2) == 0){
            $tabela = "<option value='' disabled selected hidden>Nenhum animal encontrado para esse CPF</option>";
        } else {
            $tabela = "<option value='' disabled selected hidden>Selecione um animal</option>";

            foreach ($resultado2->fetch_all() as $row) {
                $tabela = $tabela . "<option value='$row[1]'>$row[0]</option>";
            }

        }


    }
    
    $retornar = array('animais', $tabela);
    return json_encode($retornar);
}


function tabelaFunAgenCli(){
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    $header = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/Pet-Shop/pages/funcionario/agendarParaCliente.php';

    // Receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

    // Setar a quantidade de items por pagina
    $qnt_result_pg = 5;

    // Calcular o inicio visualização
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    $servico = $_GET['servico'];

    $stmt = $conn->prepare("SELECT Funcionarios.nome, data_agendamento, horario_agendamento, pk_Agendamento, COUNT(PK_Agendamento) AS num_result from Agendamentos
        LEFT JOIN Animais
        ON Agendamentos.fk_Animal = Animais.pk_Animal
        LEFT JOIN Clientes
        ON Animais.fk_Cliente = Clientes.pk_Cliente
        INNER JOIN Funcionarios
        ON Agendamentos.fk_Funcionario = Funcionarios.pk_Funcionario
        WHERE Funcionarios.nome LIKE ?
        AND `status` = 'Disponivel'
        AND tipo = ?
        ORDER BY data_agendamento, horario_agendamento");

    $pesquisar = "%" . $_GET['pesq'] . "%";
    $stmt->bind_param("ss", $pesquisar, $servico);

    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela =
        "<tr>
            <th>Profissional</th>
            <th>Data Agendamento</th>
            <th>Horário do agendamento</th>
            <th>Marcar</th>
        </tr>";

    
    // Pega cada linha da query e monta as linhas da tabela
    foreach ($resultado->fetch_all() as $row) {
        // Formata a data
        $det = "<button onclick='executeFunctions(" . '"fazerAgenParaCli"' . ", $row[3])'>Agendar</button>";

        $data = date('d/m/Y', strtotime($row[1]));
        $tabela = $tabela .
            "<tr>
                <td>$row[0]</td>
                <td>$data</td>
                <td>$row[2]</td>
                <td>$det</td>
            </tr>";
        $row_pg = $row[4];
    }

    if ($row_pg == 0){
        $tabela = "<tr>
            <th>Profissional</th>
            <th>Data Agendamento</th>
            <th>Horário do agendamento</th>
            <th>Marcar</th>
        </tr>" . "
            <tr>
                <td colspan=7>Não há agendamentos disponíveis</td>
            </tr>
            ";
    } 
    

    // Quantidade de pagina
    $quantidade_pg = ceil($row_pg / $qnt_result_pg);

    // Limitar os link antes depois
    $max_links = 2;
    $linkPaginas = "<a href='$header?pagina=1'><<</a> ";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_ant'>$pag_ant</a> ";
        }
    }

    $linkPaginas =  $linkPaginas . $pagina;

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $quantidade_pg) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_dep'>$pag_dep</a> ";
        }
    }

    $linkPaginas = $linkPaginas . " <a href='$header?pagina=$quantidade_pg'>>></a>";

    $retornar = array('tabela', $tabela, 'links', $linkPaginas);
    return json_encode($retornar);
}

function fazerAgenParaCli(){
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $fkAnimal = $_GET['idAnimal'];
    $pkAgen = $_GET['idAgen'];

    if($fkAnimal == ''){
        $_SESSION['agenCliFun'] = "Selecione um animal por favor";
    } else {
        $stmt = $conn->prepare("UPDATE Agendamentos 
        set fk_Animal = ?, status = 'Marcado'
        WHERE pk_Agendamento = ?");

        $stmt->bind_param("ss", $fkAnimal, $pkAgen);

        $stmt->execute();

        if (mysqli_affected_rows($conn)){
            $_SESSION['agenCliFun'] = "Agendamento realizado com sucesso";
        }
    }
}

function verificar(){
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');

    $cpf = $_GET['cpf'];

    $stmt = $conn->prepare("SELECT pk_Cliente from Clientes
    where cpf = ?");

    $stmt->bind_param("s", $cpf);

    // Executa o sql
    $stmt->execute();

    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    $id = $resultado->fetch_all()[0][0];

    if (mysqli_num_rows($resultado) > 0){

        $_SESSION['idCliente'] = $id;

        $tabela = "<div>
        <label for='nome'>Nome</label><br>
        <input type='text' name='nome' required>
    </div>

    <div>
        <label for='dataNasc'>Data de Nascimento</label><br>
        <input type='date' name='dataNasc' required>
    </div>

    <div>
        <label for='espec'>Espécie</label><br>
        <input type='text' name='espec' required>
    </div>

    <div>
        <label for='raca'>Raça</label><br>
        <input type='text' name='raca' required>
    </div>

    <div>
        <label for='peso'>Peso (Kg)</label><br>
        <input type='number' name='peso' step=0.01 pattern='[0-9]*' required>
    </div>

    <div>
        <label for='cor'>Cor</label><br>
        <input type='text' name='cor' required>
        
    </div>
    <input type='submit' value='Cadastrar'> 
        ";
        
    } else {
        $tabela = "Nada";
    }

    $retornar = array('formanimal', $tabela);
    return json_encode($retornar);
}

function tabelaComentarios() {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/Pet-Shop/backend/conexao.php');
    $header = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . '/Pet-Shop/pages/funcionario/comentarios.php';
    
    
    $filtroData = $_GET['data'];

    if ($filtroData == ''){ 
        $filtroData = date("Y-m-d");
    }

    $filtroMensagem = "%" . $_GET['pesq'] . "%";

    // Receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pag', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

    // Setar a quantidade de items por pagina
    $qnt_result_pg = 5;

    // Calcular o inicio visualização
    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    // String de preparação
    $stmt = $conn->prepare("SELECT nome, telefone, email, mensagem, `data`, 
    pk_Comentario FROM Comentarios
    WHERE `data` = ?
    AND mensagem LIKE ?
    ORDER BY nome LIMIT $inicio, $qnt_result_pg");

    $stmt->bind_param("ss", $filtroData, $filtroMensagem);

    // Executa o sql
    $stmt->execute();
    // Pega o resultado do banco
    $resultado = $stmt->get_result();

    // String que será retornada na tabela
    $tabela = "";

    if (mysqli_num_rows($resultado) == 0) {
        $tabela = $tabela . "
            <label>Não há mensagems no sistema</label>
            ";
    } else {
        // Pega cada linha da query e monta as linhas da tabela
        foreach ($resultado->fetch_all() as $row) {
            // Formata a data
            $data = date('d/m/Y', strtotime($row[4]));
            $tabela = $tabela .
                "   <label>Data</label>
                    <p>$data</p>
                    <label>Nome</label>
                    <p>$row[0]</p>
                    <label>Telefone</label>
                    <p>$row[1]</p>
                    <label>E-mail</label>
                    <p>$row[2]</p>
                    <label>Mensagem</label>
                    <p>$row[3]</p>
                    <label>Responder</label>
                    <p><a href='mailto:" . $row[2] . "'>Enviar email</a></p>
                <hr>";
        }
    }

    // Paginação - Somar a quantidade de usuários
    $result_pg = "SELECT COUNT(PK_Comentario) AS num_result FROM Comentarios";
    $resultado_pg = mysqli_query($conn, $result_pg);
    $row_pg = mysqli_fetch_assoc($resultado_pg);

    // Quantidade de pagina
    $quantidade_pg = ceil($row_pg['num_result'] / $qnt_result_pg);

    // Limitar os link antes depois
    $max_links = 2;
    $linkPaginas = "<a href='$header?pagina=1'><<</a> ";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_ant'>$pag_ant</a> ";
        }
    }

    $linkPaginas =  $linkPaginas . $pagina;

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $quantidade_pg) {
            $linkPaginas =  $linkPaginas . "<a href='$header?pagina=$pag_dep'>$pag_dep</a> ";
        }
    }

    $linkPaginas = $linkPaginas . " <a href='$header?pagina=$quantidade_pg'>>></a>";

    $retornar = array('tabela', $tabela, 'links', $linkPaginas);
    return json_encode($retornar);
}