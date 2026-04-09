<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentsRequisNotification extends Notification
{
    use Queueable;

    protected $sinistre;
    protected $documentsAttendus;
    protected $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct($sinistre, $documentsAttendus, $customMessage = null)
    {
        $this->sinistre = $sinistre;
        $this->documentsAttendus = $documentsAttendus;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Dans un monde idéal, on ajouterait 'nexmo', 'twilio' ou une custom channel SMS ici
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Documents requis pour votre déclaration de sinistre N°' . $this->sinistre->id)
            ->greeting('Bonjour ' . $notifiable->name . ',');

        if ($this->customMessage) {
            $mail->line($this->customMessage);
        } else {
            $mail->line('Votre déclaration de sinistre ("' . str_replace('_', ' ', $this->sinistre->type_sinistre) . '") a bien été prise en compte et validée par notre système intelligent.')
                ->line('Afin que votre assurance puisse procéder à l\'indemnisation ou aux réparations le plus rapidement possible, nous avons besoin des documents suivants :');
        }

        if (!$this->customMessage || str_contains($this->customMessage, ':')) {
            // Si on n'a pas de message custom ou si le message invite déjà à voir la liste
            foreach ($this->documentsAttendus as $doc) {
                $mail->line('- ' . $doc['nom_document']);
            }
        }

        $mail->action('Télécharger mes documents', url('/mon-espace/sinistres/' . $this->sinistre->id . '/upload-docs'))
            ->line('Merci de faire le nécessaire depuis votre espace.')
            ->line('Attention : Les documents sont analysés automatiquement par notre IA de conformité.');

        return $mail;
    }

    /**
     * Simulation d'un envoi SMS (Exemple de format si on utilisait une Voice Channel).
     */
    public function toVonage(object $notifiable)
    {
        // Simulation si package Vonage/Twilio est présent
        /* return (new \Illuminate\Notifications\Messages\VonageMessage)
                    ->content('Assurance : Des documents sont requis pour le sinistre #' . $this->sinistre->id . '. Connectez-vous sur Claims Master pour les uploader.'); */
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sinistre_id' => $this->sinistre->id,
            'message' => 'L\'IA a défini des documents requis pour votre dossier.'
        ];
    }
}
