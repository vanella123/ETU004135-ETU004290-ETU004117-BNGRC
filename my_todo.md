				PARTIE MAEVA :

TO DO LIST: Tache individuelle

	Objectif 1: Avoir une page ou l'on peut saisir les dons, qui s'insert dans ma table don apres 	validation.


DonModel.php
	[ok] Créer le constructeur
	    - Stocker la connexion PDO dans $this->db

	[ok] Créer insertDon($article_id, $quantite, $donateur, $date_saisie)
	    - Préparer requête INSERT
	    - Utiliser prepare + execute
	    - Retourner lastInsertId()

	[ok] Créer getAllDons()
	    - Faire LEFT JOIN avec article
	    - Sélectionner: d.id, a.nom AS article, d.quantite, d.donateur_nomd.date_saisie
	    			ORDER BY date_saisie DESC, id DESC
	    - Retourner fetchAll(PDO::FETCH_ASSOC)

----------------------------------------------------------------------

DonController.php
	[ok] Ajouter constructeur
	    - Récupérer Flight::db()
	    - Instancier DonModel
	    - Stocker dans $this->donModel

	[ok] Créer getAllDon()
	    - Retourner $this->donModel->getAllDons()

	[ok] Créer addDon($article_id, $quantite, $donateur, $date_saisie)
	    - Appeler insertDon()
	    - Retourner ID si succès
	    - Retourner false si erreur

----------------------------------------------------------------------

ArticleModel.php
	[ok] Créer getAllProduits()
	    - JOIN avec type_besoin
	    - ORDER BY nom ASC
	    - Retourner fetchAll()

	[ok] Créer getIdByNomArticle($nom)
	    - SELECT id WHERE nom = :nom
	    - Retourner id ou null

-----------------------------------------------------------------------

ArticleController.php
	[ok] Créer constructeur(PDO $db)
	    - Instancier ArticleModel

	[ok] Créer getAllArticles()
	    - Retourner getAllProduits()

------------------------------------------------------------------------

routes.php
	[ok] Déclarer route
	    	Flight::route('GET|POST /form_dons', function(){})

	[ok] Instancier controllers
	    - DonController
	    - ArticleController

	[ok] Si POST:
	    - Récupérer:
		$_POST['donateur']
		$_POST['article_id']
		$_POST['quantite']
		$_POST['date_saisie']

	[ok] Valider champs non vides

	[ok] Appeler addDon()

	[ok] Définir message:
	    - success si insertion OK
	    - danger si erreur

	[ok] Récupérer:
	    - articles (dropdown)
	    - dons (tableau)

	[ok] Flight::render('form_dons', [...])

--------------------------------------------------------------------------

form_dons.php
	[ok] Afficher message Bootstrap si défini

	[ok] Formulaire POST avec:
	    - donateur (text)
	    - article (select value=id)
	    - quantite (number)
	    - date_saisie (date)
	    - bouton submit

	[ok] Tableau affichant:
	    - Article
	    - Quantité
	    - Donateur
	    - Date

	    - foreach ($dons as $don)

--------------------------------------------------


##########################
# TO-DO LIST - Page Achats version 2 
##########################

--- Base de données ---
- Créer la table `achats` :
  - id (INT AUTO_INCREMENT PRIMARY KEY)
  - ville_id (INT, FK vers ville)
  - article_id (INT, FK vers article)
  - quantite (INT)
  - frais_pourcentage (DECIMAL, par défaut 10)
  - montant_total (DECIMAL)
  - date_achat (DATE)
- Ajouter les contraintes FK sur `ville_id` et `article_id`

--- Model : AchatModel ---
- __construct($db) : initialiser PDO
- getAllAchats($ville_id = null) : récupérer tous les achats ou filtré par ville
- insertAchat($ville_id, $article_id, $quantite, $frais, $date_achat) : insérer un nouvel achat et calculer montant_total
- getMontantAchat($article_id, $quantite, $frais) : calculer montant total avec frais
- getAchatById($id) : récupérer un achat spécifique

--- Controller : AchatController ---
- __construct() : instancier AchatModel
- getAllAchats($ville_id = null) : retourner liste des achats
- addAchat($ville_id, $article_id, $quantite, $frais, $date_achat) :
  - Vérifier qu’il n’y a pas de doublon (achat déjà réalisé pour ce besoin)
  - Appeler insertAchat du model
  - Retourner success / fail

--- Page view : achats.php ---
- Ajouter titre principal et sous-titre en italique
- Formulaire de saisie :
  - Sélection ville
  - Sélection article
  - Quantité
  - Frais (%)
  - Bouton Valider (gradient bleu avec hover)
- Message success / error affiché au-dessus du formulaire
- Filtre par ville pour le tableau
- Tableau des achats avec colonnes :
  - ID, Ville, Article, Quantité, Prix Unitaire, Frais (%), Montant Total, Date
- Style CSS :
  - Fond formulaire bleu clair (#e6f0ff)
  - Ligne de titre tableau bleu marine, contenu lignes en blanc sur nuances de bleu clair
  - Bordures tableau bleu
  - Hover sur ligne du tableau avec un bleu un peu plus foncé
  - Bouton Valider avec gradient bleu + effet hover
  - Titres et sous-titres en bleu marine harmonisé
- Responsiveness avec Bootstrap

--- Routes Flight ---
- Route GET|POST `/achats` :
  - Instancier AchatController, ArticleController et VilleController
  - Si POST :
    - Récupérer champs
    - Appeler addAchat
    - Définir message success / error
  - Si GET :
    - Récupérer filtre ville si présent
  - Récupérer listes villes, articles et achats
  - Render page `achats.php` avec variables

--- Tests et vérifications ---
- Vérifier insertion correcte des achats
- Vérifier calcul montant_total correct avec frais
- Vérifier filtrage par ville
- Vérifier affichage message success / error
- Vérifier style et couleurs du tableau
- Vérifier compatibilité responsive (Bootstrap)


7) FLUX COMPLET
[ ] GET /form_dons → affiche formulaire + tableau
[ ] POST formulaire → route récupère données
[ ] Route → Controller
[ ] Controller → Model
[ ] Model → INSERT base
[ ] Page recharge
[ ] Nouveau don visible dans tableau


=========================================
ARCHITECTURE UTILISÉE
=========================================

Vue → Route → Controller → Model → Base de données

        PARTIE : MIRANTO 

1-DispatchModel / Controller (OK)
dispatchTousLesDons() 
simulerDispatch()
simulerDispatchDon($don) 
getDonsAvecReste()
getTousLesBesoins()
executerDispatch($don_id)
getResteDon($don_id)
getBesoinsNonSatisfaits($article_id)

2-DashboardModel / Controller (OK)
getDashboardData() → liste ville + besoins + dons attribués
getTotalParVille() (option résumé total par ville)
mettreAJourStatuts() : Calcul et mise à jour automatique des statuts
    - met à jour statut_id en fonction des répartitions (En attente / En cours / Satisfait)

page dashbord.php : affichage ; (ok)
page simulation.php :  simuler + anuller + valider (ok)
    Simulation dans le dashboard (UI)
    Intégration de la simulation sur la page / (ligne en jaune + badge + 3 boutons Simuler/Valider/Annuler).
    Simulation de dispatch (preview) intégrée
    Ajout de la simulation (simuler sans INSERT), affichage détaillé des répartitions prévues et prévision d'état des besoins."


        PARTIE VANELLA :

======== sasie de besoin ============
 Formualire pour entrer le besoin : 
      - liste deroulante ville 
      - liste deroulante de produit
      - qauntite 
      - date de livraison
      - bouton de validation

Route : /saiseBesoin 
    - getAllVille 
    - getAllProduit

========== recapitulation des besoins ============
Version 2 : 
✅ 

Troisieme question : 
- creation de view pour afficher besoins totaux , satistfaits en montant , montant des besoins restant à satisfaire
- besoins totaux : on somme - quantite * prix unitaire
- tous ce qui ont statut "statifait : on somme - quantite * prix unitaire
- besoins restant à satisfaire : on somme - quantite * prix unitaire pour les besoins qui n'ont pas le statut "satisfait"

Page : 
Recapitulation des besoins : 
| Ville | Besoin Total | Besoin Satisfait | Besoin Restant |
|------|--------------|------------------|-----------------|
| Paris | 1000€        | 600€             | 400€            |
| Lyon  | 800€         | 500€             | 300€            |
| Marseille | 1200€      | 900€             | 300€            | 
Total : 3000€       | 2000€            | 1000€           |

Bouton actualiser en ajax 



======= model.php =======

creation d'un model php avec footer et header de theme bleu avec nuance 
comme les template je veux que ce soit moderne 

et on integre une page dans une model mais pas le contraire 

- les fonctionnalites jusqu'ici : 
    tableau de bord pour faire la distribution des dons pour les besoins
    formulaire de saisie de besoin
    formualire de saisie de don
    tableau pour voir les besoins totaux , satisfait et restant
    achat de produit pour les besoins a partir des dons en argent 
