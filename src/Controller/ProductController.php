<?php

namespace App\Controller;


use DateTime;
use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    #[Route('/admin/ajouter-un-produit', name: "create_product", methods: ['GET', 'POST'])]
    public function createProduct(Request $request, ProductRepository $repository, SluggerInterface $slugger): Response 
   {
      $product = new Product();

      $form = $this->createForm(ProductFormType::class, $product)
      ->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {

        $product->setCreatedAt(new DateTime());
        $product->setUpdateAt(new DateTime());


        # on vvariabilise le fichier de la photo en récupérant les données du formulaire (input photo)
        # on obtient un objet de type UploadedFile()
        $photo = $form->get('photo')->getData();

        if($photo) {
            $this->handleFile($product, $photo, $slugger);
            

        } // end if($photo)

        $repository->save($product, true);

        $this->addFlash('success', "Le produit est en ligne avec succès !");
        return $this->redirectToRoute('show_dashboard');

      } // end if($form)

    

      return $this->render('admin/product/form.html.twig', [
        'form' => $form->createView()
      ]);
   }// end createdProduct()

   #[Route('modifier-un-produit/{id}', name: 'update_product', methods: ['GET', 'POST'])]
   public function updateProduct(Product $product, Request $request, ProductRepository $repository, SluggerInterface $slugger): Response 
   {
    # Récupération de la photo actuelle
    $currentPhoto = $product->getPhoto();
    $form = $this->createForm(ProductFormType::class, $product, [
        'photo' => $currentPhoto
    ])
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $product->setUpdateAt(new DateTime());
            $newPhoto = $form->get('photo')->getData();

            if($newPhoto) {
                $this->handleFile($product, $newPhoto, $slugger);

            }else{
                # si pas de nouvelle photo, on resset la photo courante (actuelle).
                $product->setPhoto($currentPhoto);
            // end if($newPhoto)
            }
            $repository->save($product, true);
        } //end if($form)

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
   } // end updateProduct()

   private function handleFile(Product $product, UploadedFile $photo, SluggerInterface $slugger) 
   {
            # 1 6 Déconstruire le nom du fichier
            # a : variabilider l'extension du fichier
            $extension = '.' . $photo->guessExtension();

            # 2 - Assainir le nom du fichier (c-a-d retirer les accents et les espaces blancs)
            $safeFilename = $slugger->slug(pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME));

            # 3 - Rendre le nom du fichier unique
            # a - Reconstruire le nom du fichier
            $newFilename = $safeFilename . '.' . uniqid() . $extension;

            # 4 - Déplacer le fichier (upload dans notre application Symfony
            # gestion de erreur
            # On utilise un try/catch lorsqu'une méthode lance (theow) une Exception (erreur)
            try {
                # On a d'fini un parametre dans config/service.yaml qui est le chemin (absolu) du dossier 'uploads
                # On récupère la valeur (le paramètre) avec getParameter() et le nom du param défini dans le fichier service.yaml.
                $photo->move($this->getParameter('uploads_dir'), $newFilename);
                # si tout s'est bien passé (aucune Exception lancé) alors on doit set le nom de la photo un BDD
                $product->setPhoto($newFilename);
            }
            catch(FileException $exception) { 
                 $this->addFlash('warning', "Le fichier ne s'est pas importé correctement. Veuillez réessayer." . $exception->getMessage());
            } // end Catch
   }
  
}// ens class

