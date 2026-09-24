// Sistema de Chamados - pequenos comportamentos da interface

document.addEventListener('DOMContentLoaded', function () {

    // Pergunta antes de encerrar um chamado
    document.querySelectorAll('.js-confirmar-fechamento').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (!window.confirm('Quer mesmo encerrar este chamado?')) {
                evento.preventDefault();
            }
        });
    });

    // Some sozinho com os avisos ("chamado salvo", etc.) depois de alguns segundos.
    // Mensagens de erro dentro dos formulários ficam na tela para dar tempo de ler.
    document.querySelectorAll('.alert-dismissible').forEach(function (aviso) {
        setTimeout(function () {
            bootstrap.Alert.getOrCreateInstance(aviso).close();
        }, 6000);
    });

    // Evita enviar o mesmo formulário duas vezes por clique duplo
    document.querySelectorAll('form[method="post"]').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (evento.defaultPrevented) {
                return;
            }
            const botao = form.querySelector('button[type="submit"]');
            if (botao) {
                // Desativa no próximo ciclo para o envio do formulário não ser cancelado
                setTimeout(function () { botao.disabled = true; }, 0);
            }
        });
    });

});
