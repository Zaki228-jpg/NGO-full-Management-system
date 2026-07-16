from django.shortcuts import render
from django.http import JsonResponse
from .forms import ContactForm


def contact_page(request):

    if request.method == 'POST':

        form = ContactForm(request.POST)

        if form.is_valid():
            data = form.save()

            return JsonResponse({'status': 'success'})

        else:
            return JsonResponse({'status': 'error', 'errors': form.errors})

    return render(request, 'contact/contact.html')