import javafx.application.Application;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.geometry.Insets;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import java.time.LocalDate;

/**
 * Projet BUT 1 : Gestion Salle des Fêtes
 * Démonstration technique : Interface JavaFX et manipulation de listes observables.
 */
public class SalleFetesApp extends Application {

    private TableView<Reservation> table = new TableView<>();
    private ObservableList<Reservation> data = FXCollections.observableArrayList();

    public static void main(String[] args) {
        launch(args);
    }

    @Override
    public void start(Stage primaryStage) {
        // 1. CONFIGURATION DU TABLEAU (VUE)
        table.setEditable(true);

        TableColumn<Reservation, String> nameCol = new TableColumn<>("Nom du Client");
        nameCol.setCellValueFactory(new PropertyValueFactory<>("clientName"));
        nameCol.setMinWidth(150);

        TableColumn<Reservation, LocalDate> dateCol = new TableColumn<>("Date de l'événement");
        dateCol.setCellValueFactory(new PropertyValueFactory<>("date"));
        dateCol.setMinWidth(150);

        TableColumn<Reservation, String> typeCol = new TableColumn<>("Type d'événement");
        typeCol.setCellValueFactory(new PropertyValueFactory<>("eventType"));
        typeCol.setMinWidth(150);

        table.getColumns().addAll(nameCol, dateCol, typeCol);
        table.setItems(data);

        // 2. FORMULAIRE D'AJOUT (CONTROLEUR/INPUTS)
        TextField nameInput = new TextField();
        nameInput.setPromptText("Nom du client");

        DatePicker dateInput = new DatePicker();
        
        ComboBox<String> typeInput = new ComboBox<>();
        typeInput.getItems().addAll("Mariage", "Anniversaire", "Réunion", "Autre");
        typeInput.setPromptText("Type");
        typeInput.getSelectionModel().selectFirst();

        Button addButton = new Button("Ajouter la réservation");
        
        // Logique de l'événement (Action)
        addButton.setOnAction(e -> {
            if(nameInput.getText().isEmpty() || dateInput.getValue() == null) {
                showAlert("Erreur", "Veuillez remplir tous les champs.");
                return;
            }
            Reservation newRes = new Reservation(
                nameInput.getText(),
                dateInput.getValue(),
                typeInput.getValue()
            );
            data.add(newRes);
            nameInput.clear();
            dateInput.setValue(null);
        });

        // 3. MISE EN PAGE (LAYOUT)
        HBox formLayout = new HBox(10);
        formLayout.setPadding(new Insets(10));
        formLayout.getChildren().addAll(nameInput, dateInput, typeInput, addButton);

        VBox mainLayout = new VBox(10);
        mainLayout.setPadding(new Insets(20));
        mainLayout.getChildren().addAll(new Label("Planning des Réservations"), table, formLayout);

        Scene scene = new Scene(mainLayout, 600, 400);
        primaryStage.setTitle("Gestion Salle des Fêtes - Projet BUT 1");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    // Méthode utilitaire pour les alertes
    private void showAlert(String title, String content) {
        Alert alert = new Alert(Alert.AlertType.WARNING);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(content);
        alert.showAndWait();
    }

    /**
     * Classe Modèle interne pour représenter une Réservation (POJO)
     */
    public static class Reservation {
        private String clientName;
        private LocalDate date;
        private String eventType;

        public Reservation(String clientName, LocalDate date, String eventType) {
            this.clientName = clientName;
            this.date = date;
            this.eventType = eventType;
        }

        // Getters nécessaires pour le PropertyValueFactory de JavaFX
        public String getClientName() { return clientName; }
        public LocalDate getDate() { return date; }
        public String getEventType() { return eventType; }
    }
}