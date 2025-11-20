function confirmarExclusao(tipo, id) {
    if (confirm(`Tem certeza que deseja excluir este ${tipo}?`)) {
        window.location.href = `${tipo}.php?action=delete&id=${id}`;
    }
}

function validarFormularioLivro() {
    const titulo = document.getElementById('titulo').value.trim();
    const autor = document.getElementById('autor').value.trim();
    const quantidade = document.getElementById('quantidade_total').value;

    if (titulo === '' || autor === '') {
        alert('Título e Autor são obrigatórios!');
        return false;
    }

    if (quantidade < 1) {
        alert('Quantidade deve ser maior que zero!');
        return false;
    }

    return true;
}

function validarFormularioEmprestimo() {
    const livroId = document.getElementById('livro_id').value;
    const dataDevolucao = document.getElementById('data_devolucao_prevista').value;
    const dataEmprestimo = document.getElementById('data_emprestimo').value;

    if (livroId === '') {
        alert('Selecione um livro!');
        return false;
    }

    if (new Date(dataDevolucao) <= new Date(dataEmprestimo)) {
        alert('Data de devolução deve ser posterior à data de empréstimo!');
        return false;
    }

    return true;
}

function filtrarTabela(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td');
        let found = false;

        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }

        tr[i].style.display = found ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processando...';
            }
        });
    });

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
