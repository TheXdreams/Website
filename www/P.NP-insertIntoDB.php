<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Gestion Pétanque Pro</title>
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
   <header class="header">
    <div class="titre-header">
      <h1>¯\_(ツ)_/¯</h1>
    </div>

    <!-- menu header -->
    <nav class="header-menu">
      <div class="container">
        <ul class="navibar">
          <li class="menu-item">
            <a href="./Menu.php">Accueil</a>
          </li>
          <li class="menu-item menu-dropdown">
            <a href="./P.joueurs.php">Joueurs</a>
            <ul class="droppedMenu">
              <li class="menu-item">
                <a href="./P.NP-NouveauJoueur.php">Ajouter un joueur</a>
              </li>
              <li class="menu-item">
                <a href="./P.EP-ModifierJoueur.php">Modifier un joueur</a>
              </li>
            </ul>
          </li>
          <li class="menu-item menu-dropdown">
            <a href="./M.match.php">Match</a>
            <ul class="droppedMenu">
              <li class="menu-item">
                <a href="./M.NM-NouveauMatch.php">Ajouter un match</a>
              </li>
              <li class="menu-item">
                <a href="./M.MM-ModifierMatch.php">Modifier un match</a>
              </li>
            </ul>
          </li>
          <li class="menu-item">
              <a href="./E.Evaluation.php">Evaluation</a>
          </li>
          <li class="menu-item">
              <a href="./S.selection.php">Sélection</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  
  <footer id="IndexFooter">
    <div>
      <p>Contacts</p>
    </div>
  </footer>
</body>
</html>

<?php
include "dbaccess.php";

// Vérification des cookies
if(!isset($_COOKIE['username'])) {
    // Redirection page de connexion
    header("Location: ID-index.php");
    exit();
    }

try
{

    // Path stockage des photos
    $image_path = "../photos/";

    // Récupération des données du formulaire de NouveauJoueur.php
    $licenseJ = $_POST["license-j"];
    $nomJ = $_POST["nom-j"];
    $prenomJ = $_POST["prenom-j"];
    $d_naissanceJ = $_POST["d_naissance-j"];
    $tailleJ = $_POST["taille-j"];
    $poste_favJ = $_POST["poste_fav-j"];
    $commentaireJ=$_POST["commentaire-j"];
    $statutJ = $_POST["statut-j"];
    $photoJ = $_FILES['photo-j']['name'];

    if (isset($licenseJ,$nomJ,$prenomJ,$d_naissanceJ,$tailleJ,$poste_favJ,$commentaireJ,$statutJ,$photoJ)) {
        
        // Récupération des numéros de licenses
        $stmt = $conn->prepare('SELECT num_license FROM joueurs WHERE :newLicense = num_license');
        $stmt->bindParam(':newLicense',$licenseJ);
        $stmt->execute();

        // Vérification si le joueur existe dans la table
        if($stmt->fetch() === FALSE) 
        {
            $stmt = $conn->prepare('INSERT INTO joueurs(num_license,nom,prenom,photo,d_naissance,taille,poste_fav,commentaire,statut) VALUES(:num_license,:nom,:prenom,:photo,:d_naissance,:taille,:poste_fav,:commentaire,:statut)');
            
            // Vérifie les données entrées par rapport à la base de données
            if (strlen($licenseJ) != 8) {
                ?>
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Numéro de license invalide : doit être de 8 charactères";?>
                    </div>
                </div>
                <?php
            }elseif (strlen($nomJ) > 50) {
                ?>  
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Nom trop long";?>
                    </div>
                </div>
                <?php
            }elseif (strlen($prenomJ) > 50) {
                ?>
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Prénom trop long";?>
                    </div>
                </div>
                <?php
            }elseif ($tailleJ > 300) {
                ?>
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Taille trop grande";?>
                    </div>
                </div>
                <?php
            }elseif (strlen($poste_favJ) > 50) {
                ?>
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Poste favoris trop long";?>
                    </div>
                </div>
                <?php
            }elseif (strlen($statutJ) > 50) {
                ?>
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Statut trop long";?>
                    </div>
                </div>
                <?php
            }elseif (strlen($commentaireJ) > 150) {
                ?>
                <div class="msgContainer">
                    <div class="msgRetour">
                        <?php echo "Commentaire trop long";?>
                    </div>
                </div>
                <?php
            }else{
        
                // Préparation des paramètres avant vérification de l'image
                $stmt->bindParam(':num_license',$licenseJ);
                $stmt->bindParam(':nom', $nomJ);
                $stmt->bindParam(':prenom', $prenomJ);
                $stmt->bindParam(':d_naissance', $d_naissanceJ);
                $stmt->bindParam(':taille', $tailleJ);
                $stmt->bindParam(':poste_fav', $poste_favJ);
                $stmt->bindParam(':commentaire',$commentaireJ); 
                $stmt->bindParam(':statut', $statutJ);
    
                // Vérification des paramètres de la photo
                $errors= array();
                $photo_nom = $_FILES['photo-j']['name'];
                $photo_tmp =$_FILES['photo-j']['tmp_name'];
                $photo_taille= getimagesize($photo_tmp);
                $photo_type=$_FILES['photo-j']['type'];
                $photo_ext_tmp = explode('.',$photo_nom);
                $photo_ext=strtolower(end($photo_ext_tmp));
    
                $destination = $image_path . $photo_nom;
                    
                $extension= array("jpeg","jpg","png");
                    
                if ($photo_taille[0] > 200 || $photo_taille[1] > 200 ){
                    $errors="Dimensions trop grandes, la taille maximale est de 200 x 200. ";
                }

                if(in_array($photo_ext,$extension)=== false){
                    $errors="Extension non autorisé, choisissez un format JPEG, JPG ou PNG. ";
                }
                
                if($photo_taille < 5242880){
                    $errors="La taille du fichier doit être au max de 5 MB. ";
                }
                
                if(empty($errors)==true){
                    $stmt->bindParam(':photo', $photo_nom);

                    if ($stmt->execute()) {
                        // Ajout de la photo après vérification
                        move_uploaded_file($photo_tmp, $destination);
                        
                        // Confirmation de la modification du joueur
                        ?>
                        <div class="msgContainer">
                            <div class="msgRetour">
                                <?php echo "Joueur ajouté avec succès !";?><br><?php
                                header("refresh:5;url=./P.joueurs.php");
                                echo "Vous allez être redirigé sur la page joueur dans 3 seconds.";
                                ?>
                            </div>
                        </div>
                        <?php
                    }else{
                        ?>
                        <div class="msgContainer">
                            <div class="msgRetour">
                                <?php echo $errors; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        } else {
            // License déjà dans la base donc duplicata
            ?>
            <div class="msgContainer">
                <div class="msgRetour">
                    <?php echo "Ce joueur existe déjà";?><br><?php
                    header("refresh:5;url=./P.joueurs.php");
                    echo "Vous allez être redirigé sur la page joueur dans 3 seconds.";
                    ?>
                    
                </div>
            </div>
            <?php
        }
    } 
}catch (PDOException $e){
    echo $e->getMessage();
}
?>