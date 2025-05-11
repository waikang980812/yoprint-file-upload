<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        .filter-box { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2 style="display: inline-block; margin-right: 20px;">All Products</h2>
    <a href="{{ route('upload') }}" style="padding: 8px 16px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">
        Upload
    </a>
    <div class="filter-box">
        <label>
            Unique Key:
            <input type="text" id="filter-unique-key">
        </label>
        <button id="filter-btn">Filter</button>
        <button id="reset-btn">Reset</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Unique Key</th>
                <th>Product Title</th>
                <th>Size</th>
                <th>Color Name</th>
                <th>Piece Price</th>
                <!-- Add more columns as needed -->
            </tr>
        </thead>
        <tbody id="products-list"></tbody>
    </table>
    <script>
        async function fetchProducts(filters = {}) {
            const params = new URLSearchParams(filters).toString();
            const response = await fetch('/products/list?' + params);
            const { data } = await response.json();
            const tbody = document.getElementById('products-list');
            tbody.innerHTML = data.map(product => `
                <tr>
                    <td>${product.unique_key}</td>
                    <td>${product.product_title}</td>
                    <td>${product.size}</td>
                    <td>${product.color_name}</td>
                    <td>${product.piece_price}</td>
                </tr>
            `).join('');
        }

        document.getElementById('filter-btn').onclick = function() {
            fetchProducts({
                unique_key: document.getElementById('filter-unique-key').value,
            });
        };
        document.getElementById('reset-btn').onclick = function() {
            document.getElementById('filter-unique-key').value = '';
            fetchProducts();
        };

        // Initial load
        fetchProducts();
    </script>
</body>
</html>