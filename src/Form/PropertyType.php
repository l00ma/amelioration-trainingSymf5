<?php

namespace App\Form;

use App\Entity\Property;
use App\Entity\Spec;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heat', ChoiceType::class, ['choices' => $this->getChoices()])
            ->add('specs', EntityType::class, [
                'class' => Spec::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('city')
            ->add('address')
            ->add('postal_code')
            ->add('sold')
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'mt-2',
                ],
                'constraints' => [new All(array(
                    new Image(array(
                        'minWidth' => 500,
                        'minHeight' => 350,
                        'allowPortrait' => false,
                        'allowPortraitMessage' => 'Les photos doivent être en mode paysage uniquement.',
                        'mimeTypes' => array(
                            'image/jpeg',
                            'image/jpg',
                        ),
                        'mimeTypesMessage' => 'Les photos doivent être au format jpeg ou jpg uniquement.',
                    )),
                    new File(array('maxSize' => 1024000)),
                ))],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices()
    {
        $choices = Property::HEAT;
        $output = [];
        foreach ($choices as $k => $v) {
            $output[$v] = $k;
        }
        return $output;
    }
}
