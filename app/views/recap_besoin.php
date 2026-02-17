<style>
/* Conteneur principal */
.recap-container {
    padding: 30px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Titre */
.recap-container h2 {
    color: #2f3640;
    text-align: center;
    margin-bottom: 20px;
}

/* Tableau */
.recap-container table {
    width: 80%;
    margin: 0 auto;
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background-color: #ffffff;
    border-radius: 8px;
    overflow: hidden;
}

/* Entêtes */
.recap-container th {
    background-color: #0097e6;
    color: white;
    padding: 12px;
    text-align: center;
}

/* Cellules */
.recap-container td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #f1f2f6;
}

/* Lignes impaires */
.recap-container tr:nth-child(even) {
    background-color: #f1f2f6;
}

/* Bouton */
.recap-container button {
    display: block;
    margin: 20px auto;
    padding: 10px 25px;
    font-weight: bold;
    color: white;
    background-color: #0097e6;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.recap-container button:hover {
    background-color: #0652dd;
}
</style>

<div class="recap-container">

<h2>Récapitulatif des besoins</h2>

<table>
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
</table>

<button onclick="actualiser()">Actualiser</button>
</div>
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