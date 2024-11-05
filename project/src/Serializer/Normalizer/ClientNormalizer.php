<?php

namespace App\Serializer\Normalizer;

use App\Entity\Adresse;
use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ClientNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        // TODO: add, edit, or delete some data
        if ($object instanceof User) {
            // Récupérer les propriétés de l'entité User
            $data['nom'] = $object->getNom();
            $data['prenom'] = $object->getPrenom();
            $data['pseudo'] = $object->getPseudo();
            $data['email'] = $object->getEmail();
            $data['telephone'] = $object->getTelephone();
            $data['photo'] = $object->getPhoto();
            $data['roles'] = $object->getRoles();

            // Vérifier si l'objet est une instance de Client
            if ($object instanceof Client) {
                // Ajouter les propriétés spécifiques au Client
                $data['solde'] = $object->getSolde();

                $adresses = $object->getAdresses();
                $data['adresses'] = array_map(function (Adresse $adresse) {
                    return [
                        'id' => $adresse->getId(),
                        'rue' => $adresse->getRue(),
                        'ville' => $adresse->getVille(),
                        'codePostal' => $adresse->getCodePostal(),
                        'pays' => $adresse->getPays(),
                        'clientId' => $adresse->getClient() ? $adresse->getClient()->getId() : null,
                    ];
                }, $adresses->toArray());
            }
        }

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Client;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Client::class => true];
    }
}
