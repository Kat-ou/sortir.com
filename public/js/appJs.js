
/*
 * Gestion de l'aide utilisateur du formulaire de recherche des sorties
 */
const chkRegister = document.getElementById('events_list_form_isItMeRegister');
const chkNoRegister = document.getElementById('events_list_form_isItMeNoRegister');
chkRegister.addEventListener('click', function () {
    if (chkRegister.checked) {
        chkNoRegister.checked = false;
        chkNoRegister.disabled = true;
    } else {
        chkNoRegister.disabled = false;
    }
});
chkNoRegister.addEventListener('click', function () {
    if (chkNoRegister.checked) {
        chkRegister.checked = false;
        chkRegister.disabled = true;
    } else {
        chkRegister.disabled = false;
    }
});

