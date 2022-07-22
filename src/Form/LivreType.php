<?php

namespace App\Form;

use App\Entity\Livre;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('resume')
            ->add('extrait')
            ->add('photoCouv', FileType::class, [
                'mapped'=>false,
                'required'=>false
            ])
            ->add('teaser')
            ->add('stock')
            ->add('prix')
            ->add('categorie', EntityType::class,[
                'class'=> Categorie::class,
                'choice_label'=>'nom',
                'placeholder'=> "Choisissez une catégorie"
            ])

            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
