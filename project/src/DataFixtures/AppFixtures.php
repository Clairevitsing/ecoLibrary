<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Livre;
use App\Entity\LivreAuteur;
use App\Entity\LivreCommande;
use App\Entity\StatusCommande;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    const NBCATEGORIES = 6;
    const NBLIVRES = 40;
    const NBCOMMANDES = 8;
    const NBADRESSES = 11;
    const NBCLIENTS = 16;
    const NBSTATUSCOMMANDES = 5;
    const NBAUTEURS = 20;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        // Création des utilisateurs (clients)
        $clients = [];
        for ($i = 0; $i < self::NBCLIENTS; $i++) {
            $client = new Client();
            $client
                ->setPrenom($faker->firstName())
                ->setNom($faker->lastName())
                ->setPseudo($faker->userName())
                ->setRoles(["ROLE_CLIENT"])
                ->setPassword($this->passwordHasher->hashPassword($client, "password"))
                ->setEmail($faker->freeEmail())
                ->setPhoto($faker->imageUrl(200, 200))
                ->setTelephone($faker->phoneNumber())
                ->setSolde($faker->randomFloat(2, 0, 1000));

            $manager->persist($client);
            $clients[] = $client;

            // Création des adresses

                $adresse = new Adresse();
                $adresse
                    ->setVille($faker->city())
                    ->setCodePostal($faker->postcode())
                    ->setRue($faker->streetAddress())
                    ->setPays($faker->country())
                    ->setClient($client);

                $manager->persist($adresse);

        }

        // Création d'adresses supplémentaires et affectation aléatoire à des clients existants
        for ($j = 0; $j < self::NBADRESSES; $j++) {
            $adresse = new Adresse();
            $adresse
                ->setVille($faker->city())
                ->setCodePostal($faker->postcode())
                ->setRue($faker->streetAddress())
                ->setPays($faker->country())
                // Sélection aléatoire d'un client
                ->setClient($faker->randomElement($clients));

            $manager->persist($adresse);
        }

        // Flush les entités persistées jusqu'à maintenant pour leur attribuer un ID
        $manager->flush();

        $adminUsers = [];

            // Création des utilisateurs administrateurs
            $adminUser = new User();
            $adminUser
                ->setPrenom($faker->firstName())
                ->setNom($faker->lastName())
                ->setPseudo('admin')
                ->setRoles(["ROLE_ADMIN"])
                ->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin'))
                ->setEmail($faker->freeEmail())
                ->setTelephone($faker->phoneNumber())
                ->setPhoto('../assets/images/profile-picture.webp');

            $manager->persist($adminUser);
            $adminUsers[] = $adminUser;



        // Création des catégories
        $categories = [];
        for ($i = 0; $i < self::NBCATEGORIES; $i++) {
            $categorie = new Categorie();
            $categorie->setNomCategorie($faker->word());
            $categorie->setDescription($faker->text(300));
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        $livres = [];
        // Création des livres
        for ($i = 0; $i < self::NBLIVRES; $i++) {
            $livre= new Livre();
            $livre->setTitre($faker->word());
            $livre->setISBN($faker->isbn13());
            $livre->setImage($faker->imageUrl(200, 200));
            $livre->setStock($faker->numberBetween(1, 10));
            $livre->setDescription($faker->paragraph(1));
            $livre->setDatePublication($faker->dateTime());
            $livre->setCategorie($faker->randomElement($categories));
            $livre->setPrix($faker->randomFloat(2, 1000));
            $createdAt = new \DateTimeImmutable();
            $livre->setCreatedAt($createdAt);
            $updatedAt = new \DateTimeImmutable();
            $livre->setUpdatedAt($updatedAt);
            $isFavorited = rand(0, 1) == 1;
            $livre->setFavorited($isFavorited);
            $manager->persist($livre);
            $livres[] = $livre;
        }


        $auteurs = [];
        for ($i = 0; $i < self::NBAUTEURS; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($faker->firstName());
            $auteur->setPrenom($faker->lastName());
            $auteur->setBiographie($faker->text(300));


            $manager->persist($auteur);
            $auteurs[] = $auteur;
        }

        $manager->flush();

        // Associer les livres aux auteurs
        foreach ($auteurs as $auteur) {
            $nbLivres = $faker->numberBetween(1, 5);
            for ($j = 0; $j < $nbLivres; $j++) {
                $livre = $faker->randomElement($livres);
                if ($livre) {
                    $livreAuteur = new LivreAuteur();
                    $auteur->addLivreAuteur($livreAuteur);
                    $livre->addLivreAuteur($livreAuteur);

                    $manager->persist($livreAuteur);
                }
            }
        }

        $manager->flush();


        // Création des statuts de commande
        $statusCommandes = [];
        for ($i = 0; $i < self::NBSTATUSCOMMANDES; $i++) {
            $statusCommande = new StatusCommande();
            $statusCommande->setLibelle($faker->word());

            $manager->persist($statusCommande);
            $statusCommandes[] = $statusCommande;
        }


        $commandes = [];
        // Création des commandes
        for ($i = 0; $i < self::NBCOMMANDES; $i++) {
            $commande = new Commande();
            $commande->setCommandeReference($faker->uuid())
                ->setDateCommande(new DateTime($faker->date()))
                ->setTotal($faker->randomFloat(2, 10, 1000))
                ->setClient($faker->randomElement($clients))
                ->setStatusCommande($faker->randomElement($statusCommandes));

            if (!empty($livres)) {
                // Nombre aléatoire de livres par commande
                $nbLivres = $faker->numberBetween(1, 5);
                for ($j = 0; $j < $nbLivres; $j++) {
                    $livre = $faker->randomElement($livres);
                    $quantite = $faker->numberBetween(1, 5);
                    $livreCommande = new LivreCommande($commande, $livre, $quantite);
                    //$commande->addLivre($livre);
                    $commande->addLivreCommande($livreCommande);

                }
            }
            $manager->persist($commande);
            $commandes[] = $commande;

        }
        

        $manager->flush();
    }
}



