<ul class="menu_items">
 <li>
    <a type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light @if($active=='Customer') active @endif" id="button">
        <label id="title">KUNDENDATEN</label>
        <i class="feather icon-user"></i>
    
    </a>
</li>
   
<li>
    <a type="button"
        class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light @if($active=='Subject') active @endif"
        id="button">
        <i class="feather icon-image"></i>
        <label id="title">Betreff und Deckblatt</label>
    </a>

</li>

<li>
    <a type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light @if($active=='Product') active @endif" id="button">
        <i class="fa fa-industry"></i>
        <label id="title">PRODUKT</label>
    </a>
    
</li>

<li>
    <a type="button"
        class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light @if($active=='Product_details') active @endif"
        id="button">
        <i class="feather icon-file-text"></i>
        <label id="title">PRODUKT DETAILS</label>
    </a>

</li>

<li>
    <a type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light" id="button">
      <i class="fa fa-lightbulb-o"></i>
        <label id="title">STROMVERBRAUCH</label>
    </a>
</li>

<li>
    <a type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light" id="button">
        <i class="fa fa-home"></i>
        <label id="title">OBJEKT</label>
    </a>
</li> 
</ul>
