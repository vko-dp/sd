<?php

use app\assets\widgets\CartAsset;

CartAsset::register($this);
?>

<div class="top-cart">
    <ul style="list-style: none;">
        <li>
            <a href="javascript:void(0);">
                <span class="cart-icon"><i class="fa fa-shopping-cart"></i></span>
                 <span class="cart-total">
                     <span class="cart-title">корзина</span>
                     <span class="cart-item">2 item(s)- </span>
                     <span class="top-cart-price">$365.00</span>
                 </span>
            </a>
        </li>
    </ul>
</div>