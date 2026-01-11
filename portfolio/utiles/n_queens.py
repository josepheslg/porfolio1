import matplotlib.pyplot as plt
import matplotlib.patches as patches

class NQueensSolver:
    """
    Résolveur du problème des N-Dames utilisant l'algorithme de Backtracking.
    """

    def __init__(self, n=8):
        self.n = n
        self.solutions = []  # Liste pour stocker toutes les solutions trouvées

    def solve(self):
        """Lance la résolution du problème."""
        board = [-1] * self.n  # board[row] = col
        self._backtrack(board, 0)
        print(f"Terminé : {len(self.solutions)} solutions trouvées pour {self.n} dames.")

    def _is_safe(self, board, current_row, current_col):
        """Vérifie si une dame peut être placée en (current_row, current_col)."""
        for row in range(current_row):
            col = board[row]
            
            # Vérification colonne (la ligne est implicitement différente)
            if col == current_col:
                return False
            
            # Vérification diagonales (différence absolue)
            if abs(current_row - row) == abs(current_col - col):
                return False
        
        return True

    def _backtrack(self, board, row):
        """Fonction récursive de backtracking."""
        # Cas de base : toutes les dames sont placées
        if row == self.n:
            self.solutions.append(board[:]) # On copie la solution
            return

        # On essaie toutes les colonnes pour la ligne actuelle
        for col in range(self.n):
            if self._is_safe(board, row, col):
                board[row] = col
                self._backtrack(board, row + 1)
                # Pas besoin de réinitialiser board[row], il sera écrasé à la prochaine itération

    def display_text_solution(self, index=0):
        """Affiche une solution en mode texte (console)."""
        if not self.solutions:
            print("Aucune solution trouvée.")
            return

        board = self.solutions[index]
        print(f"\n--- Solution {index + 1} ---")
        for row in range(self.n):
            line = ""
            for col in range(self.n):
                if board[row] == col:
                    line += " Q "
                else:
                    line += " . "
            print(line)

    def plot_solution(self, index=0):
        """Génère une image graphique de l'échiquier avec Matplotlib."""
        if not self.solutions:
            print("Aucune solution à afficher.")
            return

        solution = self.solutions[index]
        fig, ax = plt.subplots(figsize=(6, 6))
        
        # Dessiner l'échiquier
        for row in range(self.n):
            for col in range(self.n):
                # Couleur alternée (blanc/noir)
                color = '#DDB88C' if (row + col) % 2 == 0 else '#A66D4F'
                rect = patches.Rectangle((col, self.n - 1 - row), 1, 1, linewidth=0, facecolor=color)
                ax.add_patch(rect)
                
                # Placer la dame si nécessaire
                if solution[row] == col:
                    # On place un cercle ou un texte pour représenter la dame
                    ax.text(col + 0.5, self.n - 1 - row + 0.5, '♛', 
                            fontsize=30, ha='center', va='center', color='black')

        # Paramètres d'affichage
        ax.set_xlim(0, self.n)
        ax.set_ylim(0, self.n)
        ax.set_xticks([])
        ax.set_yticks([])
        ax.set_title(f"Problème des {self.n} Dames - Solution {index + 1}/{len(self.solutions)}")
        
        plt.show()

# --- BLOC D'EXÉCUTION ---
if __name__ == "__main__":
    # 1. Initialisation pour 8 dames
    solver = NQueensSolver(n=8)
    
    # 2. Résolution
    solver.solve()
    
    # 3. Affichage console de la première solution
    solver.display_text_solution(0)
    
    # 4. Affichage graphique (nécessite matplotlib)
    try:
        solver.plot_solution(0) # Affiche la solution 1
        # solver.plot_solution(10) # Décommentez pour voir la 11ème solution
    except Exception as e:
        print("\nErreur graphique : Assurez-vous d'avoir installé matplotlib (pip install matplotlib)")