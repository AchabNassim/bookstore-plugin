let chosenContainer = "controls";

// Display chosen container and hides the others.
const toggleContainer = (chosenContainer) => {
    const containers = document.getElementsByClassName('formContainer');
    const selectedContainer = document.getElementById(`${chosenContainer}Container`);

    for (let i = 0; i < containers.length; i++) {
        containers[i].classList.add('hidden');
        console.log(containers[i]);
    }
    selectedContainer.classList.remove('hidden');
}

const fillForm = (e, formName) => {
    const form = document.getElementById(formName);
    const idInputField = form.querySelector('.idInputField');
    const categoryInputField = form.querySelector('.nameInputField');

    const row = e.target.parentElement.parentElement;
    const idValue = row.querySelector('.idTd').getAttribute('value');
    const categoryValue = row.querySelector('.categoryTd').textContent;

    idInputField.value = idValue;
    categoryInputField.value = categoryValue;
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

registerCtrlBtnEvent();
registerDeleteBtnEvent();
registerUpdateBtnEvent();

