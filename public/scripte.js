function approveArticle(articleId) {
    if (!confirm('Êtes-vous sûr de vouloir approuver cet article ?')) {
        return;
    }

    console.log('Approving article:', articleId); // Debug line

    fetch('../../action/approveArticle.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `article_id=${articleId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove article from pending list or update UI
            const articleElement = document.getElementById(`article-${articleId}`);
            if (articleElement) {
                articleElement.remove();
            }
            // Show success message
            alert(data.message);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'approbation');
    });
}