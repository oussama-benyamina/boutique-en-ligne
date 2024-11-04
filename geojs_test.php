<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geoapify Test with Autocomplete</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 5px; }
        #autocomplete-results { 
            position: absolute; 
            border: 1px solid #ccc; 
            background: white; 
            z-index: 1000; 
            max-height: 200px; 
            overflow-y: auto;
        }
        .autocomplete-item { padding: 5px; cursor: pointer; }
        .autocomplete-item:hover { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Geoapify Test with Autocomplete</h1>
    
    <form id="register-form-submit">
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required autocomplete="off">
            <div id="autocomplete-results"></div>
        </div>
        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>
        </div>
        <div class="form-group">
            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" required>
        </div>
        <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" required>
        </div>
        <button type="submit">Submit</button>
    </form>

    <script>
    const apiKey = '64e7261cd1e24ae1be5beb40a6347fa1';
    const addressInput = document.getElementById('address');
    const autocompleteResults = document.getElementById('autocomplete-results');

    let debounceTimer;

    addressInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = this.value;
            if (query.length > 2) {
                fetchAddressSuggestions(query);
            } else {
                autocompleteResults.innerHTML = '';
            }
        }, 300);
    });

    function fetchAddressSuggestions(query) {
        const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(query)}&apiKey=${apiKey}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data.features);
            })
            .catch(error => console.error('Error:', error));
    }

    function displaySuggestions(suggestions) {
        autocompleteResults.innerHTML = '';
        suggestions.forEach(suggestion => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            div.textContent = suggestion.properties.formatted;
            div.addEventListener('click', () => selectAddress(suggestion.properties));
            autocompleteResults.appendChild(div);
        });
    }

    function selectAddress(address) {
        document.getElementById('address').value = address.formatted;
        document.getElementById('city').value = address.city || '';
        document.getElementById('postal_code').value = address.postcode || '';
        document.getElementById('country').value = address.country || '';
        autocompleteResults.innerHTML = '';
    }

    // Close autocomplete results when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== addressInput && e.target !== autocompleteResults) {
            autocompleteResults.innerHTML = '';
        }
    });

    // Prevent form submission for this example
    document.getElementById('register-form-submit').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
    });
    </script>
</body>
</html>