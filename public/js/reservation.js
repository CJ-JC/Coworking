function Description(event) {
    let container = document.getElementById("container");
    let aboutDescription = document.getElementById("about-description");
  
    if (aboutDescription) {
      container.classList.add("description");
      aboutDescription.classList.add("description");
      document.body.style.overflow = 'hidden';
    }

    event.preventDefault();
}

function openImage(event) {
    let container = document.getElementById("container");
    let cardImages = document.getElementById("card-images");
  
    if (cardImages) {
      container.classList.add("description");
      cardImages.classList.add("description");
      document.body.style.overflow = 'hidden';
    }
    event.preventDefault();
}

function showSubscription(event) {
    // Récupérer l'ID de l'abonnement à partir de l'attribut "data-id" de l'icône
    let subscriptionId = event.target.dataset.id;
    
    // Sélectionner tous les divs de description commençant par "description-"
    let descriptionDivs = document.querySelectorAll("[id^='description-']");
    
    // Sélectionner les éléments container et cardSubscription
    let container = document.getElementById("container");
    let cardSubscription = document.getElementById("card-subscription");

    // Vérifier si le cardSubscription existe
    if (cardSubscription) {
        // Ajouter la classe "description" aux éléments container et cardSubscription
        container.classList.add("description");
        cardSubscription.classList.add("description");
        
        // Masquer toutes les descriptions en définissant "display: none"
        descriptionDivs.forEach(function(div) {
            div.style.display = "none";
        });
        
        // Sélectionner le div de description correspondant à l'abonnement cliqué
        let descriptionDiv = document.getElementById("description-" + subscriptionId);
        
        // Vérifier si le div de description existe
        if (descriptionDiv) {
            // Afficher le div de description en définissant "display: block"
            descriptionDiv.style.display = "block";
        }
        
        // Empêcher le comportement par défaut du clic sur l'icône
        event.preventDefault();
        
        // Désactiver le défilement de la page
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(event) {
    let aboutDescription = document.getElementById("about-description");
    let container = document.getElementById("container");
    let cardImages = document.getElementById("card-images");
    let cardSubscription = document.getElementById("card-subscription");

    cardSubscription.classList.remove("description");
    aboutDescription.classList.remove("description");
    container.classList.remove("description");
    cardImages.classList.remove("description");
    document.body.style.overflow = 'scroll';
    event.preventDefault();
}

// Écouteur d'événement pour détecter les changements dans les cases à cocher
$('.form-check-input').on('change', function() {
    // Obtenez la case à cocher sélectionnée
    var selectedCheckbox = $('.form-check-input:checked');
    
    // Récupérez le prix du workspace
    var workspacePrice = parseFloat($('#total-price').data('workspace-price'));
    
    // Vérifiez si une case à cocher est sélectionnée
    if (selectedCheckbox.length > 0) {
        // Récupérez le prix de la case à cocher sélectionnée
        var subscriptionPrice = parseFloat(selectedCheckbox.data('price'));
        
        // Vérifiez si le prix de l'abonnement est un nombre valide
        if (!isNaN(subscriptionPrice)) {
            // Calculez le prix total en ajoutant le prix du workspace et de l'abonnement
            var totalPrice = workspacePrice + subscriptionPrice;
            // Affichez le prix total
            $('#total-price').text('Total : ' + totalPrice.toFixed(2) + '€');
        } else {
            // Affichez un message d'erreur si le prix de l'abonnement n'est pas valide
            $('#total-price').text('Total : ' + workspacePrice.toFixed(2) + '€');
        }
    } else {
        // Affichez le prix du workspace si aucune case à cocher n'est sélectionnée
        $('#total-price').text('Total : ' + workspacePrice.toFixed(2) + '€');
    }
});

function increaseCount(event, element, fieldName) {
    var input = element.previousElementSibling;
    var value = parseInt(input.value, 10);
    value = isNaN(value) ? 0 : value;

    if (value < 10) { // Vérifie si la valeur est inférieure à 10
        value++;
    }
    
    input.value = value;

    // Mettez à jour le champ hidden associé à "numberPassengers"
    var hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = fieldName;
    hiddenInput.value = value;
    input.parentNode.appendChild(hiddenInput);
}

function decreaseCount(event, element, fieldName) {
    var input = element.nextElementSibling;
    var value = parseInt(input.value, 10);
    if (value > 1) {
        value = isNaN(value) ? 0 : value;
        value--;
        input.value = value;

        // Mettez à jour le champ hidden associé à "numberPassengers"
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = fieldName;
        hiddenInput.value = value;
        input.parentNode.appendChild(hiddenInput);
    }
}