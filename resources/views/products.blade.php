<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        .editable {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <h3 class="text-center">Product Form</h3>
            <form id="productForm">
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="quantity_in_stock" class="form-label">Quantity in Stock</label>
                        <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock"
                            required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="price_per_item" class="form-label">Price per Item</label>
                        <input type="number" step="0.01" class="form-control" id="price_per_item" name="price_per_item"
                            required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
            </form>
        </div>

        <h3 class="mt-5">Submitted Products</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity in Stock</th>
                    <th>Price per Item</th>
                    <th>Total Value</th>
                    <th>Datetime Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTable">
                @foreach ($products as $index => $product)
                    <tr data-index="{{ $index }}">
                        <td>{{ $product['product_name'] }}</td>
                        <td>{{ $product['quantity_in_stock'] }}</td>
                        <td>{{ $product['price_per_item'] }}</td>
                        <td>{{ $product['total_value'] }}</td>
                        <td>{{ $product['datetime_submitted'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-btn" data-index="{{ $index }}">Edit</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            let editIndex = null;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            $('#productForm').on('submit', function (e) {
                e.preventDefault();
    
                const data = {
                    product_name: $('#product_name').val(),
                    quantity_in_stock: $('#quantity_in_stock').val(),
                    price_per_item: $('#price_per_item').val()
                };
    
                const url = editIndex === null ? '/products' : `/products/${editIndex}`;
                const method = editIndex === null ? 'POST' : 'PUT';
    
                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            if (editIndex === null) {
                                $('#productTable').append(`
                                    <tr data-index="${response.index}">
                                        <td>${response.product.product_name}</td>
                                        <td>${response.product.quantity_in_stock}</td>
                                        <td>${response.product.price_per_item}</td>
                                        <td>${response.product.total_value}</td>
                                        <td>${response.product.datetime_submitted}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-btn" data-index="${response.index}">Edit</button>
                                        </td>
                                    </tr>
                                `);
                            } else {
                                const row = $(`tr[data-index="${editIndex}"]`);
                                row.find('td:nth-child(1)').text(response.product.product_name);
                                row.find('td:nth-child(2)').text(response.product.quantity_in_stock);
                                row.find('td:nth-child(3)').text(response.product.price_per_item);
                                row.find('td:nth-child(4)').text(response.product.total_value);
                                row.find('td:nth-child(5)').text(response.product.datetime_submitted);
                                editIndex = null;
                                $('#submitBtn').text('Save');
                            }
                            $('#productForm')[0].reset();
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('something wrone and error is: ' + error);
                    }
                });
            });
    
            $(document).on('click', '.edit-btn', function () {
                const row = $(this).closest('tr');
                editIndex = $(this).data('index');
                $('#product_name').val(row.find('td:nth-child(1)').text());
                $('#quantity_in_stock').val(row.find('td:nth-child(2)').text());
                $('#price_per_item').val(row.find('td:nth-child(3)').text());
                $('#submitBtn').text('Update');
            });
        });
    </script>
    
</body>

</html>
