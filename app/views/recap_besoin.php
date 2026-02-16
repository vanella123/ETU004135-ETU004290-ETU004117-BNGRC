<style>
    /* Style général */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f6fa;
    padding: 30px;
}

/* Titre */
h2 {
    color: #2f3640;
    text-align: center;
    margin-bottom: 20px;
}

/* Tableau */
table {
    width: 80%;
    margin: 0 auto;
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background-color: #ffffff;
    border-radius: 8px;
    overflow: hidden;
}

/* Entêtes */
th {
    background-color: #0097e6;
    color: white;
    padding: 12px;
    text-align: center;
    font-size: 16px;
}

/* Cellules */
td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #f1f2f6;
    font-size: 15px;
}

/* Lignes impaires */
tr:nth-child(even) {
    background-color: #f1f2f6;
}

/* Bouton */
button {
    display: block;
    margin: 20px auto;
    padding: 10px 25px;
    font-size: 16px;
    font-weight: bold;
    color: white;
    background-color: #0097e6;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

/* Hover bouton */
button:hover {
    background-color: #0652dd;
    transform: scale(1.05);
}

/* Responsive */
@media screen and (max-width: 600px) {
    table {
        width: 100%;
    }
    th, td {
        padding: 10px;
        font-size: 14px;
    }
    button {
        width: 80%;
        font-size: 14px;
    }
}
</style>

<h2>Récapitulatif des besoins</h2>

<table border="1">
    <tr>
        <th>Besoin Total</th>
        <th>Besoin Satisfait</th>
        <th>Besoin Restant</th>
    </tr>
    <tr>
        <td id="total">-</td>
        <td id="satisfait">-</td>
        <td id="restant">-</td>
    </tr>
</tabl e>

<br>

<button onclick="actualiser()">Actualiser</button>
<script>
function actualiser(){
    fetch('/resumeBesoinsAjax')
        .then(response => response.json())
        .then(data => {

            document.getElementById("total").innerHTML =
                parseFloat(data.besoins_totaux).toFixed(2) + " €";

            document.getElementById("satisfait").innerHTML =
                parseFloat(data.besoins_satisfaits).toFixed(2) + " €";

            document.getElementById("restant").innerHTML =
                parseFloat(data.besoins_restants).toFixed(2) + " €";
        })
} 
</script>