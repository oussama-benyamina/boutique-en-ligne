<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$user_connected = isset($_SESSION['client_id']) && !empty($_SESSION['client_id']);
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
?>

<!--PreLoader-->
<div class="loader">
    <div class="loader-inner">
        <div class="circle"></div>
    </div>
</div>
<!--PreLoader Ends-->

<!-- header -->
<div class="top-header-area" id="sticker">
    <div class="container" style="max-width: 100%;">
        <div class="row">
            <div class="col-lg-12 col-sm-12 text-center">
                <div class="main-menu-wrap">
                    <!-- logo -->
                    <div class="site-logo">
                        <a href="index.php">
                            <img src="assets/img/logo.png" alt="" id="logo">
                        </a>
                    </div>
                    <!-- logo -->

                    <!-- menu start -->
                    <nav class="main-menu">

                    
                        <ul>
                            <li class="<?php echo ($current_page == 'index.php') ? 'current-list-item' : ''; ?>"><a href="index.php">Home</a></li>
                            <li class="<?php echo ($current_page == 'about.php') ? 'current-list-item' : ''; ?>"><a href="about.php">About</a></li>
                            <li class="<?php echo ($current_page == 'contact.php') ? 'current-list-item' : ''; ?>"><a href="contact.php">Contact</a></li>
                            <li class="<?php echo ($current_page == 'shop.php') ? 'current-list-item' : ''; ?>" style="position: relative;">
                                <a href="shop.php">Shop</a>
                                <ul class="sub-menu">
                                    <li class="<?php echo ($current_page == 'checkout.php') ? 'current-list-item' : ''; ?>"><a href="checkout.php">Watches</a></li>
                                    <li class="<?php echo ($current_page == 'single-product.php') ? 'current-list-item' : ''; ?>"><a href="single-product.php">Jewelerry</a></li>
                                </ul>
                            </li>
                           
                            <li class="shopping-ct">

                            <div class="header-icons">
                                    <a class="shopping-cart" href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                                    
                                </div>
                                <div class="header-icons">
                                <?php if ($user_connected): ?>
                                        <a class="wishlist" href="wishlist.php"><i class="fas fa-heart"></i></a>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <li>
                                 
                                <div class="header-icons">
                                    <?php if ($user_connected): ?>
                                        <div class="dropdown" style="display: flex;">
                                            <a class="profile-icon" href="javascript:void(0);" onclick="toggleDropdown(event)">
                                                <span><i class="fas fa-user user-logged-in"></i></span>
                                            </a>
                                            <ul class="sub-menu dropdown-menu" style="display: none;  justify-content: center; flex-direction: column; text-align: center;">
                                                <li><a href="profile.php" style="color:black;">Profile</a></li>
                                                <li><?php if ($user_connected && $_SESSION['user_role'] == 'support' ||  $_SESSION['user_role'] == 'admin' ): ?><a href="inv/inventory.php" style="color:black;">Inventory</a><?php endif; ?></li>
                                                <li><a href="my-orders.php" style="color:black;">My Orders</a></li>
                                                <li><?php if ($user_connected && ($_SESSION['user_role'] == 'support')): ?><a href="#" id="order-management-link" style="color:black;">Order Management</a><?php endif; ?></li>
                                                <li><?php if ($user_connected && ($_SESSION['user_role'] == 'admin') ||  $_SESSION['user_role'] == 'admins'): ?><a href="./admin_order_details.php" style="color:black;">Order Management</a><?php endif; ?></li>
                                                <li><a href="functions/logout.php" style="color:red;">Disconnect</a></li>
                                                <li><?php if ($user_connected && $_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'admins'): ?><a href="./admin_files/admin.php" style="color:blue;">Admin</a><?php endif; ?> </li>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <a class="profile-icon" href="javascript:void(0);" onclick="openPopup()">
                                            <span><i class="fas fa-user"></i></span>
                                        </a>
                                    <?php endif; ?>
                                   
                                </div>
                            </li>

                        </ul>
                    </nav>
                    <div id="search" style="display: flex;justify-content: center; flex-direction: column;">           
                        <form action="search_results.php" method="get" class="search-form">
                                        <input type="text" name="query" class="search-input" placeholder="Search..." id="search-input" autocomplete="off">
                                        <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
                        </form>
                        <div id="search-suggestions" class="autocomplete-suggestions"></div></div>
                    
                    
                    
                        <div class="mobile-menu">
          
                    </div>
                    <!-- menu end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end header -->

<?php if ($user_connected && $user_role === 'support' && !isset($_SESSION['has_signed'])): ?>
    <div id="signature-popup" class="popup" style="display: none;">
        <div class="popup-content">
            <span class="close" onclick="closeSignaturePopup()">&times;</span>
            <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <p>Veuillez signer ci-dessous pour confirmer votre présence.</p>

            <canvas id="signature-pad" width="400" height="200"></canvas>

            <form id="signature-form">
                <input type="hidden" name="signature" id="signature-data">
                <button type="button" class="btn btn-secondary" id="clear-button">Effacer</button>
                <button type="submit" class="btn btn-primary" id="submit-button">Signer</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    $(document).ready(function() {
        let isExpanded = false;
        let currentQuery = '';

        // Hide suggestions on page load
        $('#search-suggestions').hide();

        $('#search-input').on('focus', function() {
            setTimeout(() => {
                isExpanded = true;
                if (currentQuery.length > 1) {
                    fetchSuggestions(currentQuery, true);
                }
            }, 400);
        });

        $('#search-input').on('blur', function() {
            isExpanded = false;
            // Optionally hide suggestions when focus is lost
            setTimeout(() => {
                $('#search-suggestions').hide();
            }, 200);
        });

        $('#search-input').on('input', function() {
            currentQuery = $(this).val();
            if (currentQuery.length > 1) {
                fetchSuggestions(currentQuery, isExpanded);
            } else {
                $('#search-suggestions').hide();
            }
        });

        function fetchSuggestions(query, fetchImages) {
            $.ajax({
                url: 'autocomplete.php',
                method: 'GET',
                data: {
                    query: query,
                    fetchImages: fetchImages
                },
                success: function(data) {
                    let suggestions = JSON.parse(data);
                    // Hide suggestions if there are no results
                    if (suggestions.length === 0) {
                        $('#search-suggestions').hide();
                        return;
                    }

                    let suggestionList = '';

                    suggestions.forEach(function(suggestion) {
                        if (suggestion.type === 'product') {
                            if (fetchImages && suggestion.image_url) {
                                suggestionList += `
                                    <div class="autocomplete-suggestion" data-type="product" data-id="${suggestion.id}">
                                        <img src="${suggestion.image_url}" alt="${suggestion.name}" class="suggestion-image">
                                        <span class="suggestion-text">${suggestion.name}</span>
                                    </div>`;
                            } else {
                                suggestionList += `
                                    <div class="autocomplete-suggestion" data-type="product" data-id="${suggestion.id}">
                                        <span class="suggestion-text">${suggestion.name}</span>
                                    </div>`;
                            }
                        } else if (suggestion.type === 'category') {
                            suggestionList += `
                                <div class="autocomplete-suggestion" data-type="category" data-name="${suggestion.name}">
                                    <i class="fas fa-folder category-icon"></i>
                                    <span class="suggestion-text">${suggestion.name} (Category)</span>
                                </div>`;
                        }
                    });

                    $('#search-suggestions').html(suggestionList).show();
                }
            });
        }

        $(document).on('click', '.autocomplete-suggestion', function() {
            let type = $(this).data('type');
            let id = $(this).data('id');
            let categoryName = $(this).data('name');

            if (type === 'product') {
                window.location.href = 'single-product.php?id=' + id;
            } else if (type === 'category') {
                window.location.href = 'shop.php?category=' + categoryName.toLowerCase();
            }
        });
    });

    window.onscroll = function() {
        var logo = document.getElementById('logo');
        if (window.scrollY === 0) {
            logo.src = 'assets/img/logo.png';
        } else {
            logo.src = 'assets/img/logob.png';
        }
    };

    function toggleDropdown(event) {
        event.preventDefault();
        event.stopPropagation();
        var dropdownMenu = document.querySelector('.dropdown-menu');
        dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'flex' : 'flex';
    }

    document.addEventListener('click', function(event) {
        var dropdownMenu = document.querySelector('.dropdown-menu');
        var profileIcon = event.target.closest('.profile-icon');
        if (!profileIcon && dropdownMenu && dropdownMenu.style.display === 'flex') {
            dropdownMenu.style.display = 'none';
        }
    });

    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === 'flex') {
                    openDropdown.style.display = 'none';
                }
            }
        }
    };

    // Signature pad functionality
    var signaturePad;

    function initSignaturePad() {
        var canvas = document.getElementById('signature-pad');
        signaturePad = new SignaturePad(canvas);
    }

    function openSignaturePopup() {
        document.getElementById('signature-popup').style.display = 'block';
        initSignaturePad();
    }

    function closeSignaturePopup() {
        document.getElementById('signature-popup').style.display = 'none';
    }

    $('#clear-button').on('click', function() {
        signaturePad.clear();
    });

    $('#signature-form').on('submit', function(e) {
        e.preventDefault();
        if (signaturePad.isEmpty()) {
            alert('Veuillez signer avant de soumettre.');
        } else {
            var dataURL = signaturePad.toDataURL();
            $('#signature-data').val(dataURL);

            // Send the signature data to the server
            $.ajax({
                url: 'admin_files/support_signature.php',
                method: 'POST',
                data: {
                    signature: dataURL
                },
                success: function(response) {
                    if (response === 'success') {
                        window.location.href = 'order_management.php';
                    } else {
                        alert('An error occurred while submitting the signature.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });

    // Modify the order management link click event
    $('#order-management-link').on('click', function(e) {
        e.preventDefault();
        <?php if (!isset($_SESSION['has_signed'])): ?>
            openSignaturePopup();
        <?php else: ?>
            window.location.href = 'order_management.php';
        <?php endif; ?>
    });
</script>



<style>
    li.shopping-ct {
        display: flex !important;
    }

    /* Style de la barre de recherche */
    .search-form {
        display: inline-flex;
        align-items: center;
        background-color: #f0f0f0;
        border-radius: 20px;
        padding: 5px;
        margin-left: 15px;
        height: 35px;
    }
    #search {
    display: flex;
    justify-content: center;
    flex-direction: column;
    position: relative; /* Add this line */
}
    .search-input {
        border: none;
        outline: none;
        background: none;
        padding: 5px;
        font-size: 14px;
        width: 120px;
        transition: width 0.4s ease;
    }

    .search-input:focus {
        width: 180px;
    }

    .search-button {
        background: none;
        border: none;
        color: #333;
        cursor: pointer;
        padding: 5px;
    }

    .search-button i {
        font-size: 16px;
    }

    .autocomplete-suggestions {
        display: none; /* Initially hide the suggestions */
        background-color: white;
        position: absolute;
        z-index: 9999;
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        flex-direction: column;
        top: 50px;
        right: 0px;
        transition: width 0.4s ease;
        width: 150px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .search-input:focus ~ .autocomplete-suggestions {
        width: 300px;
    }

    .autocomplete-suggestion {
        display: flex;
        align-items: center;
        padding: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .suggestion-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        margin-right: 10px;
        border-radius: 5px;
    }

    .suggestion-text {
        flex: 1;
    }

    .autocomplete-suggestions div {
        padding: 10px;
        cursor: pointer;
    }

    .autocomplete-suggestions div:hover {
        background-color: #f0f0f0;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-menu {
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 9999 !important;
        right: 0;
       
    }

    .dropdown-menu li {
        padding: 5px;
        text-decoration: none;
        display: block;
    }

    .dropdown-menu li:hover {
        background-color: #f1f1f1;
    }

    .user-logged-in {
        background-color: #2F9985;
        border-radius: 50%;
        padding: 5px;
    }

    .popup {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .popup-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #signature-pad {
        border: 1px solid #000;
        margin-bottom: 10px;
    }


    @media screen and (max-width: 768px) {
        #search {
            position: absolute; 
            right: 50px;
            top: 15px;
        }
        .search-input { 
            width: 100px;
            z-index: 1000;
        }
        .search-input:focus {
            width: 120px;
        }

        li.shopping-ct {display: unset !important;}
    
}

@media screen and (min-width: 769px) and (max-width: 1025px) {
    #search {
            position: absolute; 
            right: 50px;
            top: 15px;
        }
        .search-input { 
            width: 100px;
            z-index: 1000;
        }
        .search-input:focus {
            width: 120px;
        }
        li.shopping-ct {display: unset !important;}

}


@media screen and (min-width: 1025px) {
    li.shopping-ct {
        float: right;
        right: 210px;
    }
    nav.main-menu > ul > li:first-child {
        margin-left: 100px;
    }
}

.site-logo {
    display: flex;
    align-items: center;
}

.main-menu-wrap {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 20px;
}

@media screen and (max-width: 1025px) {
    .main-menu-wrap {
        justify-content: space-between;
        padding: 10px;
    }

    .site-logo {
        position: absolute;
        top: 12px;
    }

    .site-logo img {
        max-width: 100px;
    }
}

@media screen and (min-width: 1025px) {
    .main-menu-wrap {
        justify-content: flex-start;
        gap: 30px;
    }

    .site-logo {
        margin-right: 0;
    }
}

</style>

