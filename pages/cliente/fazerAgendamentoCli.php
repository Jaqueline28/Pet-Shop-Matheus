<?php
include_once("../../rotas.php");
include_once($connRoute);
require_once $funcoesRoute;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animais</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/5998/5998796.png">
    <link rel="stylesheet" href="cssBack/modalfechamento.css">
    <link rel="stylesheet" href="cssBack/lista.css">
    <style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        text-align: center;
    }

    tr:nth-child(odd) {
        background-color: #dddddd;
    }
    </style>
    <script src="<?php echo $functionsRoute; ?>"></script>
</head>

<body onresize="checaDispositivo()">
    <?php
    if (!loged()) {
        $_SESSION['msglogin'] = "Por favor, faça o login primeiro.";
        header("Location: " . $loginCliRoute);
    }
    if (isset($_SESSION['msgCadAnimaisCli'])) {
        echo $_SESSION['msgCadAnimaisCli'];
        unset($_SESSION['msgCadAnimaisCli']);
    }
    ?>

    <form action="<?php echo $proc_cadAnimalRoute; ?>" method="post">
        <label for="nome">Tipo de Agendamento</label><br>
        <select name="tipoAgen" id="tipoAgen" onchange="gerarTabelaFazAgenCli()">
            <option value="" disabled selected hidden>Selecione o tipo de Agendamento</option>
            <option value="Banho">Banho</option>
            <option value="Tosa">Tosa</option>
            <option value="Veterinário">Veterinário</option>
        </select><br><br>

        <label for="dataAgen">Data de Agendamento</label><br>
        <input type="date" id="dataAgen" onchange="gerarTabelaFazAgenCli()"><br><br>

        <table id="fazAgend"></table>

        <input type="submit" value="Cadastrar">
    </form>

    <button onclick="executeFunctions('logoff')">Logoff</button>
    <script src="<?php echo $dataHojeRoute; ?>"></script>
</body>

</html>