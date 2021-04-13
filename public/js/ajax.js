// on récupère les elements HTML Select liés au ville :
const selectVille = document.getElementById('event_form_ville');
const spanPostCode = document.getElementById('event_form_postcode');
// on récupère les elements HTML Select liés au Lieu :
const selectLocation = document.getElementById('event_form_location');
const spanStreet = document.getElementById('event_form_street');
const spanLatitude = document.getElementById('event_form_latitude');
const spanLongitude = document.getElementById('event_form_longitude');


// évenement sur l'élément Select HTML de la ville :
addEmptyOptionIntoSelect(selectVille);
selectVille.addEventListener('change', function () {
    var url = "create/location/" + this.value;
    // Requete AJAX
    fetch(url, {method: 'GET'})
        .then(function (response) {
            return response.json();
        }).then(function (data) {
            // 1- on retire les valeurs des élements liés à un lieu (cas ou on re-sélectionne une ville)
            removeAllChildren(selectLocation);
            removeElementsLocationInForm();
            // on crée un élément HTML Option par Lieu et on l'ajoute au DOM :
            for (const location of data.locationsInCity) {
                let option = document.createElement("option");
                if (location.length !== 0) {
                    option.text = location.name;
                    option.value = location.id;
                } else {
                    option.text = "";
                }
                selectLocation.add(option);
            }

            // 2- on valorise le code postal
            spanPostCode.innerHTML = data.postCode;

            // 3- On ajoute un évenement sur l'element Select des Lieux :
            selectLocation.addEventListener('change', function(){
                let choiceLocation = getLocationInList(data.locationsInCity, selectLocation);
                // On valorise les elements HTML Select "Rue, Lat. et Long." :
                if (choiceLocation !== null) {
                    spanStreet.innerHTML = choiceLocation.street;
                    spanLatitude.innerHTML = choiceLocation.latitude;
                    spanLongitude.innerHTML = choiceLocation.longitude;
                }
            } )
        })
});

/**
 * Méthode retournant le Lieu sélectionné dans l'élement HTML de type Select.
 * Dans le cas ou le champ null est sélectionné, il y a effacement des champs "Rue, Latitude, et longitude".
 * @param listLocations - La liste de Lieux complète (id, name, lat. , long. , street) retourné par la requete AJAX.
 * @param elementSelect - L'élement HTML de type Select précédement valorisé (id et name) par la requete AJAX.
 * @returns Lieu | null - Un lieu s'il est trouvé, sinon null.
 */
function getLocationInList(listLocations, elementSelect) {
    let result = null;
    for (const place of listLocations) {
        // si le lieu choisi dans le formulaire (elementSelect.value) correspond
        // à un lieu de la liste qui a été retournée (listLocations) :
        if (elementSelect.value == place.id) {
            result = place;
            break;
        }
    }
    if (result === null) {
        removeElementsLocationInForm();
    }
    return result;
}


/**
 * Procédure qui supprime tous les elements enfants d'un éléments HTML parent passé en paramètre dans le DOM.
 * @param select - Element HTML de type Select.
 */
function removeAllChildren(select) {
    while (select.lastElementChild) {
        select.removeChild(select.lastElementChild);
    }
}

/**
 * Procédure d'effacement des champs correspondant au lieu sélectionné (Rue, Lat., Long.).
 */
function removeElementsLocationInForm() {
    spanStreet.innerHTML = "";
    spanLatitude.innerHTML = "";
    spanLongitude.innerHTML = "";
}


/**
 * Procédure de création d'un champ vide dans un élément HTML select passé en paramètre.
 * @param selectHtml - L'élement Html Select.
 */
function addEmptyOptionIntoSelect(selectHtml) {
    let option = document.createElement('option');
    option.value = '0';
    option.selected = true;
    selectHtml.insertAdjacentElement('afterbegin', option);
}