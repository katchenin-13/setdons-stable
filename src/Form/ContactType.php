<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\Communaute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('nom')
            // ->add('fonction')
            // ->add('email')
            // ->add('numero')
            // ->add('observation')
            // ->add('code', null, [
            //     'label' => 'Code',
            //     'attr' => ['placeholder' => 'Saisir le code']])
            ->add('nom', TextType::class,[
                'label' =>'Nom et Prénom',
                 "required" => true
           ])
           ->add('fonction', TextType::class,[
            'label' =>'Fonction',
             "required" => true,
           ])
           ->add('email', EmailType::class,[
            'label' =>'Email',
            'required' => false
           ])
            ->add('numero', TextType::class, [
                'label' => 'Nnuméro de téléphone',
                'attr' => ['placeholder' => '+2250154865252'],
                'constraints' => [new Regex([
                    'pattern' => '/[0-9]{14,16}/',
                    'match' => true,
                    'message' => 'Le numero de téléphone contient seulement des chiffre de 0-9',
                ])]

            ])
        

         
          ->add('observation', TextareaType::class,[
            'label' =>'Observation',
            'required' => false
           ])   
           
            // ->add('UpdatedAt')
            // ->add('CreatedAt')
            // ->add('utilisateur')
            ->add('communaute', EntityType::class, [
                'class'        => Communaute::class,
                'label'        => 'Communaute',
                "required" => true,
                'choice_label' => 'libelle',
                'multiple'     => false,
                'expanded'     => false,
                'placeholder' => 'Choisir une localité',
                'attr' => ['class' => 'has-select2'],
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
