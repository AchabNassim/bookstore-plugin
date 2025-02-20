let chosenContainer = "controls";

// Display a chosen container and hides the others.
const toggleContainer = (chosenContainer) => {
    const containers = document.getElementsByClassName('formContainer');
    const selectedContainer = document.getElementById(`${chosenContainer}Container`);

    for (let i = 0; i < containers.length; i++) {
        containers[i].classList.add('hidden');
    }
    selectedContainer.classList.remove('hidden');
}

// takes array of class names, returns array of dom elements.
const retrieveDomElements = (form, classes) => {
    const domElements = {};
    classes.forEach((className) => domElements[className] = form.querySelector(`.${className}`));
    return (domElements);
}

// takes array of class names, returns their text content.
const retrieveTextValues = (row, classes) => {
    const values = {};
    classes.forEach((className) => values[className] = row.querySelector(`.${className}Td`).textContent.trim());
    return (values);
}

// fill the form fields with the appropriate value if the field is not null
const fillInputFields = (idValue, coverValue, fields, values) => {
    if (fields['idInputField']) 
        fields['idInputField'].value = idValue;
    if (fields['titleInputField']) 
        fields['titleInputField'].value = values.title;
    if (fields['authorInputField']) 
        fields['authorInputField'].value = values.author;
    if (fields['descriptionInputField']) 
        fields['descriptionInputField'].value = values.description;
    if (fields['priceInputField']) 
        fields['priceInputField'].value = Number(values.price);
    if (fields['urlInputField']) 
        fields['urlInputField'].value = coverValue;  
};


// This function sets the select box value, it needs to check if the chosen category still exists in the db
const fillSelectFields = (fields, values) => {
    if (fields['categoryInputField'] && fields['subcategoryInputField']) {
        const hasCategory = [...fields['categoryInputField'].options].some((option) => option.value === values.category);
        const hasSubcategory = [...fields['subcategoryInputField'].options].some((option) => option.value === values.subcategory);

        if (hasCategory && hasSubcategory) {
            fields['categoryInputField'].value = values.category.trim();
            fields['subcategoryInputField'].value = values.subcategory.trim().length > 0 ? values.subcategory.trim() : "None";
        } else {
            fields['categoryInputField'].value = "";
            fields['subcategoryInputField'].value = "";
        }
    }
}

// retrieves data from table and fills the selected form with the data
const fillForm = (e, formName) => {
    const inputClassNames = ['idInputField', 'titleInputField', 'authorInputField', 'descriptionInputField', 'priceInputField', 'categoryInputField', 'subcategoryInputField', 'urlInputField'];
    const fieldClassNames = ['title', 'author', 'description', 'price', 'category', 'subcategory'];
    
    const form = document.getElementById(formName);
    const row = e.target.parentElement.parentElement;
    const inputs = retrieveDomElements(form, inputClassNames);
    const values = retrieveTextValues(row, fieldClassNames);
    const idValue = row.querySelector('.idTd').getAttribute('value');
    const coverValue = row.querySelector('.coverTd').getAttribute('value');

    fillInputFields(idValue, coverValue, inputs, values);
    fillSelectFields(inputs, values);
}

const registerCtrlBtnEvent = () => {
    const ctrlButtons = document.getElementsByClassName('ctrlButton');
    for (let i = 0; i < ctrlButtons.length; i++) {
        ctrlButtons[i].addEventListener('click', (e) => {
            if (e.target.textContent !== "back" && e.target.textContent !== "cancel") {
                toggleContainer(e.target.value);
            } else {
                toggleContainer("controls");
            }
        })
    };
}

const registerUpdateBtnEvent = () => {
    const updateButtons = document.getElementsByClassName('editButton');
    for (let i = 0; i < updateButtons.length; i++) {
        updateButtons[i].addEventListener('click', (e) => {
            fillForm(e, 'updateContainer');
            toggleContainer('update');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

const registerDeleteBtnEvent = () => {
    const deleteButtons = document.getElementsByClassName('deleteButton');
    for (let i = 0; i < deleteButtons.length; i++) {
        deleteButtons[i].addEventListener('click', (e) => {
            fillForm(e, 'deleteContainer');
            toggleContainer('delete');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

// This function fills the subcategory select box based on the chosen category
const categorySelectEvent = () => {
    const categoryInput = document.getElementsByClassName('categoryInputField');
    for (let i = 0; i < categoryInput.length; i++) {
            categoryInput[i].addEventListener('change', (e) => {
                // fetch the id of the chosen category from the categories select box
                const element = e.target.selectedOptions[0];
                const parentId = element.getAttribute('data-category-id');
                const subcategoryInput = e.target.nextElementSibling;
                
                // If no category is selected hide the subcategory select box
                if (parentId)
                    subcategoryInput.classList.remove("hidden");
                else
                    subcategoryInput.classList.add("hidden");
                // Hide all the select options
                for (let j = 0; j < subcategoryInput.options.length; j++) {
                    subcategoryInput.options[j].classList.remove('hidden');
                }

                // loop through the subcategory select options and only display those that have the same id as the chosen category
                for (let i = 0; i < subcategoryInput.options.length; i++) {
                    const childId =  subcategoryInput.options[i].dataset.parentId;
                    const childValue =  subcategoryInput.options[i].value;

                    if (childValue != "None" && childId !== parentId ) {
                        subcategoryInput.options[i].classList.add('hidden');
                    }
                }
            })
    }
}

registerCtrlBtnEvent();
registerDeleteBtnEvent();
registerUpdateBtnEvent();
categorySelectEvent();