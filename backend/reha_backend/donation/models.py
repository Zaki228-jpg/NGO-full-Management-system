from django.db import models

# Create your models here.
from django.db import models


class Donation(models.Model):

    donor_name = models.CharField(max_length=100)

    email = models.EmailField()

    amount = models.DecimalField(max_digits=10, decimal_places=2)

    message = models.TextField(blank=True)

    donated_at = models.DateTimeField(auto_now_add=True)


    def __str__(self):
        return self.donor_name