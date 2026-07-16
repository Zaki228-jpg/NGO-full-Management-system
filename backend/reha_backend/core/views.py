
from django.shortcuts import render
from projects.models import Project
from news.models import News


def home(request):

    projects = Project.objects.all().order_by('-created_at')[:6]
    news = News.objects.all().order_by('-created_at')[:6]

    return render(request, 'core/index.html', {
        'projects': projects,
        'news': news
    })


def about(request):
    return render(request,'core/about.html')


def vision(request):
    return render(request,'core/vision-mission.html')


def values(request):
    return render(request,'core/core-values.html')


def education(request):
    return render(request,'core/education.html')


def health(request):
    return render(request,'core/health.html')


def emergency(request):
    return render(request,'core/emergency.html')


def women(request):
    return render(request,'core/women.html')


def livelihood(request):
    return render(request,'core/livelihood.html')


def kabul(request):
    return render(request,'core/kabul.html')


def herat(request):
    return render(request,'core/herat.html')


def wardak(request):
    return render(request,'core/wardak.html')


def takhar(request):
    return render(request,'core/takhar.html')


def panjshir(request):
    return render(request,'core/panjshir.html')


def ongoingprojects(request):
    return render(request,'core/ongoingprojects.html')


def contact(request):
    return render(request,'core/contact.html')


def donate(request):
    return render(request,'core/donate.html')


def news(request):
    return render(request,'core/news.html')


def events(request):
    return render(request,'core/events.html')


def press(request):
    return render(request,'core/press.html')


def announcements(request):
    return render(request,'core/announcements.html')