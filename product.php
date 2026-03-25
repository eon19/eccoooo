<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
            <link rel="stylesheet" href="bootstrap-5.3.8-examples/assets/dist/css/bootstrap.min.css">

            <style>
                body{
                    font-family: 'Trebuchet MS', Arial, sans-serif;
                    background:#f5f5f5;
                }

                header{
                    background-color: #a5d859;
                    color: white;
                    padding: 15px 25px;
                }

                h2{
                    
                    text-align:center;
                    margin:30px 0;
                    font-weight:600;
                }

                .prod-grid{
                    display:grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    max-width: 1400px;
                    margin:auto;
                    gap:25px;
                }

                .prod-card{
                    background:white;
                    display:flex;
                    flex-direction:column;
                    border-radius:12px;
                    box-shadow:0 4px 10px rgba(0,0,0,0.08);
                    padding:18px;
                    height:100%;
                    transition:.2s ease;
                }

                .prod-card:hover{
                    transform:translateY(-5px);
                }

                .img-holder{
                    height:220px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                }

                .img-holder img{
                    max-height:200px;
                    max-width:100%;
                    object-fit:contain;
                }

                .card-content{
                    flex-grow:1;
                    text-align:center;
                }

                .card-content h3{
                    font-size:20px;
                    min-height:55px; 
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin-top:10px;
                }

                .price{
                    color:#a35c00;
                    font-weight:bold;
                    margin-bottom:5px;
                }

                .stocks{
                    font-size:14px;
                    color:#555;
                }

                .btn-grp{
                    margin-top:auto;
                    text-align:center;
                }

                .buy-now{
                    background:#a5d859;
                    color:white;
                    border:none;
                    padding:8px 30px;
                    border-radius:20px;
                    font-weight:600;
                    transition:.2s ease;
                }

                .buy-now:hover{
                    background:white;
                    color:#a5d859;
                    border:1px solid #a5d859;
                }

                .confirm-btn{
                    background:#a5d859;
                    border:none;
                    padding:10px;
                    border-radius:20px;
                    color:white;
                    font-weight:bold;
                    width:100%;
                }

                .confirm-btn:hover{
                    background:white;
                    color:#a5d859;
                    border:1px solid #a5d859;
                }

                #modalTotalAmount{
                    color:tomato;
                    font-weight:bold;
                }
    </style>
    </head>

    <body>

    <header>
        <h1></h1>
    </header>

    <div class="container my-4">
    <h2>Products</h2>

    <?php
    include 'db.php';
    $sql = "SELECT * FROM products WHERE qty > 0";
    $result = $conn->query($sql);
    ?>

    <div class="prod-grid">
    <?php
    if($result->num_rows > 0){
        while($product = $result->fetch_assoc()){
    ?>
    <div class="prod-card">

        <div class="img-holder">
            <img src="images/<?php echo $product['img']; ?>" alt="Product">
        </div>

        <div class="card-content">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <div class="price">₱<?php echo number_format($product['price'],2); ?></div>
            <div class="stocks">Stocks: <?php echo $product['qty']; ?></div>
        </div>

        <div class="btn-grp">
            <button class="buy-now"
            onclick="openBuyNow(
            <?php echo $product['product_id']; ?>,
            '<?php echo addslashes($product['name']); ?>',
            <?php echo $product['price']; ?>,
            <?php echo $product['qty']; ?>,
            '<?php echo $product['img']; ?>'
            )">
            Buy Now
            </button>
        </div>

    </div>
    <?php
        }
    }else{
        echo "<p>No products available.</p>";
    }
    $conn->close();
    ?>
    </div>
    </div>


    <div class="modal fade" id="buyNowModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

    <div class="modal-header">
    <h5 class="modal-title">Purchase Product</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body text-center">
    <form method="POST" action="purchase.php">

    <input type="hidden" name="product_id" id="modalProductId">

    <img id="modalProductImage" class="img-fluid mb-3"
    style="max-height:200px;object-fit:contain;">

    <p><strong>Product:</strong> <span id="modalProductName"></span></p>
    <p><strong>Price:</strong> ₱<span id="modalProductPrice"></span></p>
    <p><strong>Available Stocks:</strong> <span id="modalProductStocks"></span></p>

    <div class="mb-3 text-start">
    <label>Quantity</label>
    <input type="number" name="quantity" id="modalQuantity"
    class="form-control" value="1" min="1" oninput="updateTotal()">

    <label class="mt-2">Name</label>
    <input type="text" name="client_name" class="form-control" required>

    <label class="mt-2">Contact</label>
    <input type="tel" name="client_contact"
    class="form-control" maxlength="11" required>

    <p class="mt-3">
    <strong>Total Amount:</strong>
    ₱<span id="modalTotalAmount">0.00</span>
    </p>
    </div>

    <button type="submit" class="confirm-btn">Confirm Purchase</button>
    </form>
    </div>

    </div>
    </div>
    </div>

    <script>
    let currentPrice = 0;
    let maxStocks = 0;

    function openBuyNow(id,name,price,stocks,image){
    currentPrice = price;
    maxStocks = stocks;

    document.getElementById("modalProductId").value=id;
    document.getElementById("modalProductName").innerText=name;
    document.getElementById("modalProductPrice").innerText=price.toFixed(2);
    document.getElementById("modalProductStocks").innerText=stocks;
    document.getElementById("modalProductImage").src="images/"+image;

    let qty=document.getElementById("modalQuantity");
    qty.value=1;
    qty.max=stocks;

    updateTotal();

    new bootstrap.Modal(document.getElementById("buyNowModal")).show();
    }

    function updateTotal(){
    let qty=document.getElementById("modalQuantity").value;

    if(qty>maxStocks) qty=maxStocks;
    if(qty<1 || isNaN(qty)) qty=1;

    document.getElementById("modalQuantity").value=qty;
    document.getElementById("modalTotalAmount").innerText=
    (currentPrice*qty).toFixed(2);
    }
    </script>

    <script src="bootstrap-5.3.8-examples/assets/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>