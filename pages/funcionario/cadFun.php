<?php
include_once("../../rotas.php");
include_once($connRoute);


if (isset($_SESSION['msgCadFun'])) {
    echo $_SESSION['msgCadFun'];
    unset($_SESSION['msgCadFun']);
}

if ($_SESSION['tipo'] != "admin") {
    // $_SESSION['msgRotaProibida'] = "Você Não possui permissão para entrar nessa página";
    header("Location: " . $agendamentoFunRoute);
}

if (!isset($_SESSION['tipo'])) {
    $_SESSION['msgRotaProibidaCli'] = "Você Não possui permissão para entrar nessa página";
    header("Location: " . $homeRoute);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css-dinamico/meu-perfil.css">
    <link rel="stylesheet" href="../css-estatico/header.css">
    <link rel="stylesheet" href="../css-dinamico/header-corporativo.css">
    <link rel="stylesheet" href="../css-dinamico/cadastrar-funcionario.css">


</head>

<body>
    
    
    <div>

        <div class="informacoes-superior">
            <img src="../img-dinamico/icone-cadastro-funcionario.svg" alt="ícone do meu perfil">
            <h1>CADASTRE UM FUNCIONÁRIO NA PLATAFORMA!</h1>
        </div>

        <form action="<?php echo $procCadFunRoute; ?>" method="post">

            <div class="box-maior-input">


                <div class="box-input">
                    <label for="nome">NOME: </label>
                    <input type="text" name="nome" id="nome" required autofocus placeholder="Digite o Nome do funcionário">
                </div>

                <div class="box-input">
                    <label for="cpf">CPF: </label>
                    <input type="text" name="cpf" pattern="[0-9]{11}" placeholder="Digite o CPF do funcionário" required>
                </div>



            </div>


            <div class="box-maior-input">

                <div class="box-input">

                    <label for="profissao">PROFISSÃO</label>
                    <select name="profissao" id="profissao" required>
                        <option value="" disabled selected hidden>Selecione a profissão do funcionário</option>
                        <option value="Veterinario">Veterinário</option>
                        <option value="Secretaria">Secretária</option>
                        <option value="Esteticista">Esteticista</option>
                    </select>

                </div>

            </div>

            <div class="box-maior-input">

                <div class="box-input">

                    <label for="senha">SENHA: </label>
                    <input type="password" name="senha" placeholder="Digite a senha" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^\w\s]).{8,}$" required>

                </div>



                <div class="box-input">

                    <label for="confsenha">CONFIRME A SENHA: </label>
                    <input type="password" name="confsenha" placeholder="Digite a senha" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^\w\s]).{8,}$" required>

                </div>

            </div>

            <div class="box-inferior-botoes">

                <p id="senhanaoigual"></p>

                <input type="submit" id="cadastrar" value="Cadastrar" disabled></input>

                <a href="<?php echo $agendamentoFunRoute; ?>">VOLTAR</a>

            </div>

        </form>



    </div>
    <script src="<?php echo $confSenhaRoute; ?>"></script>
</body>

</html>