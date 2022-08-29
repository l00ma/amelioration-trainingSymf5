<?php

namespace App\Form;

use App\Entity\PropertySearch;
use App\Entity\Spec;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PropertySearchType extends AbstractType
{

    private $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codePostal', TextType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'code postal'
                ]
            ])
            ->add('minSurface', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'surf mini'
                ]
            ])
            ->add('maxSurface', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'surf maxi'
                ]
            ])
            ->add('nbrRooms', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'pièces'
                ]
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'budget maxi'
                ]
            ])
            ->add('options', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Spec::class,
                'choice_label' => 'name',
                'multiple' => true
            ]);
        // si on est un utlisateur connecté, on a un token et on affiche un champ supplémentaire pour chercher une annonce par son id
        if (!empty($this->token->getToken())) {
            $builder->add('nbid', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => '#'
                ]
            ])
                ->add('isSold', CheckboxType::class, [
                    'required' => false,
                    'label' => 'vendu',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
