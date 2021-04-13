// on récupère les elements HTML Select liés au ville :
const selectVille = document.getElementById('event_form_ville');
const selectPostCode = document.getElementById('event_form_postcode');
// on récupère les elements HTML Select liés au Lieu :
const selectLocation = document.getElementById('event_form_location');
const selectStreet = document.getElementById('event_form_street');
const selectLatitude = document.getElementById('event_form_latitude');
const selectLongitude = document.getElementById('event_form_longitude');


// évenement sur l'élément Select HTML de la ville :
selectVille.addEventListener('change', function () {
    var url = "create/location/" + this.value;
    // Requete AJAX
    fetch(url, {method: 'GET'})
        .then(function (response) {
            return response.json();
        }).then(function (data) {
            // 1- on retire les valeurs des élements liés à un lieu (cas ou on re-sélectionne une ville)
            resetFormElementsLocation();
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
            removeAllChildren(selectPostCode);
            let optionPostCode = document.createElement("option");
            optionPostCode.text = data.postCode;
            selectPostCode.add(optionPostCode);

            // 3- On ajoute un évenement sur l'element Select des Lieux :
            selectLocation.addEventListener('change', function(){
                let choiceLocation = getLocationInList(data.locationsInCity, selectLocation);
                // On valorise les elements HTML Select "Rue, Lat. et Long." :
                if (choiceLocation !== null) {
                    addElementsAboutLocationInForm(choiceLocation);
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
        removeAllChildren(selectStreet);
        removeAllChildren(selectLatitude);
        removeAllChildren(selectLongitude);
    }
    return result;
}

/**
 * Méthode supprimant tous les éléments enfants des 4 éléments HTML de type Select parents "Rue, Latitude, Longitude, Lieu" dans le DOM.
 * Nota : Permet la remise à zéro des champs lors d'une re-sélection d'une ville.
 */
function resetFormElementsLocation() {
    let tabSelect = [selectStreet, selectLatitude, selectLongitude, selectLocation];
    for (const select of tabSelect) {
        removeAllChildren(select);
    }
}

/**
 * Méthode qui supprime tous les elements enfants d'un éléments HTML parent passé en paramètre dans le DOM.
 * @param select - Element HTML de type Select.
 */
function removeAllChildren(select) {
    while (select.lastElementChild) {
        select.removeChild(select.lastElementChild);
    }
}

/**
 * Méthode qui ajoute les valeurs dans les élémnets HTML Select "Rue, Latitude et Longitude" dans le DOM.
 * @param choiceLocation - Le lieu choisi par l'utilisateur.
 */
function addElementsAboutLocationInForm(choiceLocation) {
    // Rue
    removeAllChildren(selectStreet);
    let optionStreet = document.createElement("option");
    optionStreet.text = choiceLocation.street;
    selectStreet.add(optionStreet);
    // Latitude
    removeAllChildren(selectLatitude);
    let optionLatitude = document.createElement("option");
    optionLatitude.text = choiceLocation.latitude;
    selectLatitude.add(optionLatitude);
    // Longitude
    removeAllChildren(selectLongitude);
    let optionLongitude = document.createElement("option");
    optionLongitude.text = choiceLocation.longitude;
    selectLongitude.add(optionLongitude);
}