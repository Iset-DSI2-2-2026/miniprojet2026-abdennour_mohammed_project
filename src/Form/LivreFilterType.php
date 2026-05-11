<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'required' => false,
                'attr' => ['placeholder' => 'Recherche partielle'],
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'nom',
                'label' => 'Genre',
                'required' => false,
                'placeholder' => 'Tous',
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Uniquement disponibles',
                'required' => false,
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nom',
                'label' => 'Tag',
                'required' => false,
                'placeholder' => 'Tous',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
