<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Fetch all menus ordered by category and id
$stmt = $koneksi->query("SELECT * FROM menus ORDER BY FIELD(category, 'Makanan', 'Minuman', 'Tambahan'), id ASC");
$menus = $stmt->fetch_all(MYSQLI_ASSOC);

// Group menus by category
$grouped_menus = [];
foreach ($menus as $menu) {
    $grouped_menus[$menu['category']][] = $menu;
}

$category_icons = [
    'Makanan' => 'fa-utensils',
    'Minuman' => 'fa-glass-cheers',
    'Tambahan' => 'fa-plus-circle'
];

// Determine categories to show tabs
$categories = array_keys($grouped_menus);
?>

<section class="hero">
    <div class="container hero-content">
        <h1>Rasa Mewah, Layanan Tanpa Batas</h1>
        <p>Hadirkan pengalaman kuliner tak terlupakan di setiap acara Anda bersama Savoria Catering. Dari pertemuan elegan hingga perayaan spesial.</p>
        <a href="#menu" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem; border-radius: 30px;">Eksplorasi Menu</a>
    </div>
</section>

<section id="menu" class="menu-section">
    <div class="container">
        <h2 class="section-title">Katalog Menu Premium</h2>
        
        <!-- Tabs Navigation -->
        <div class="menu-tabs">
            <?php foreach ($categories as $index => $category): ?>
                <button class="tab-btn <?= $index === 0 ? 'active' : '' ?>" onclick="openTab(event, 'tab-<?= strtolower($category) ?>')">
                    <i class="fas <?= $category_icons[$category] ?? 'fa-concierge-bell' ?>"></i> 
                    <?= htmlspecialchars($category) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Tabs Content -->
        <div class="tab-content-container">
            <?php foreach ($grouped_menus as $index => $categoryData): 
                $categoryName = $categoryData[0]['category']; // Get category name from first item
            ?>
                <div id="tab-<?= strtolower($categoryName) ?>" class="tab-content <?= $index === $categories[0] ? 'active' : '' ?>">
                    <div class="menu-grid">
                        <?php foreach ($categoryData as $menu): ?>
                            <div class="menu-card">
                                <?php 
                                    $img = empty($menu['image']) ? 'default_food.jpg' : $menu['image'];
                                ?>
                                <div class="menu-image" style="background-image: url('assets/images/<?= htmlspecialchars($img) ?>');"></div>
                                <div class="menu-content">
                                    <h4 class="menu-title"><?= htmlspecialchars($menu['name']) ?></h4>
                                    <p class="menu-desc"><?= htmlspecialchars($menu['description']) ?></p>
                                    <div class="menu-price">Rp <?= number_format($menu['price'], 0, ',', '.') ?></div>
                                    
                                    <form action="add_to_cart.php" method="POST" class="d-flex add-to-cart-form" style="gap: 10px;">
                                        <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 70px; text-align: center;" required>
                                        <button type="submit" class="btn btn-outline" style="flex-grow: 1; padding: 0.5rem; font-size: 0.95rem;">
                                            <i class="fas fa-cart-plus"></i> Tambah
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script>
function openTab(evt, tabName) {
    // Hide all tab contents
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
        tabContents[i].classList.remove("active");
    }

    // Remove active class from all buttons
    const tabBtns = document.getElementsByClassName("tab-btn");
    for (let i = 0; i < tabBtns.length; i++) {
        tabBtns[i].className = tabBtns[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    const activeTab = document.getElementById(tabName);
    activeTab.style.display = "block";
    // Slight delay to allow display block to apply before animating opacity
    setTimeout(() => {
        activeTab.classList.add("active");
    }, 10);
    
    evt.currentTarget.className += " active";
}

// Initialize first tab manually if needed, though CSS handles initial state usually.
// Hide all except first initially
document.addEventListener("DOMContentLoaded", function() {
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 1; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
