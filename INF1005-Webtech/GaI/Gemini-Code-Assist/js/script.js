document.addEventListener('DOMContentLoaded', function() {
    const addPetBtn = document.getElementById('add-pet-btn');
    if (addPetBtn) {
        let petCount = 1;
        addPetBtn.addEventListener('click', function() {
            petCount++;
            const petsContainer = document.getElementById('pets-container');
            const newPetForm = document.createElement('div');
            newPetForm.classList.add('pet-form', 'mb-3', 'border', 'p-3');
            newPetForm.innerHTML = `
                <h4>Pet ${petCount}</h4>
                <div class="mb-3">
                    <label for="pet_name[]" class="form-label">Pet's Name</label>
                    <input type="text" class="form-control" name="pet_name[]" required>
                </div>
                <div class="mb-3">
                    <label for="pet_breed[]" class="form-label">Breed</label>
                    <input type="text" class="form-control" name="pet_breed[]" required>
                </div>
                <div class="mb-3">
                    <label for="pet_age[]" class="form-label">Age</label>
                    <input type="number" class="form-control" name="pet_age[]" required>
                </div>
                <div class="mb-3">
                    <label for="pet_photo[]" class="form-label">Pet's Photo</label>
                    <input type="file" class="form-control" name="pet_photo[]" accept="image/*">
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-pet-btn">Remove</button>
            `;
            petsContainer.appendChild(newPetForm);
        });

        const petsContainer = document.getElementById('pets-container');
        petsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-pet-btn')) {
                e.target.parentElement.remove();
            }
        });
    }
});
