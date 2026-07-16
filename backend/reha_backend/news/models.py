from django.db import models

# Create your models here.
from django.db import models


class News(models.Model):

    title = models.CharField(max_length=200)

    image = models.ImageField(upload_to='news/', blank=True, null=True)
    


    content = models.TextField()

    category = models.CharField(max_length=100)

    published_date = models.DateField()

    created_at = models.DateTimeField(auto_now_add=True)

    video_url = models.URLField(blank=True, null=True)

    external_link = models.URLField(blank=True, null=True)

    def __str__(self):
        return self.title
    
