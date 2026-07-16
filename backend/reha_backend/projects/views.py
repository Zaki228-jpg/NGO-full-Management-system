from django.shortcuts import render

# Create your views here.
from django.shortcuts import render
from .models import Project


def projects_list(request):

    projects = Project.objects.all()

    return render(request, 'projects/projects.html', {
        'projects': projects
    })

from django.shortcuts import render, get_object_or_404
from .models import Project


def project_detail(request, id):

    project = get_object_or_404(Project, id=id)

    return render(request, 'projects/project_detail.html', {
        'project': project
    })